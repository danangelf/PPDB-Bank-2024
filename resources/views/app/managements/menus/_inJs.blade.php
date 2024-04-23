
<script>
    <x-app.datatable.datatablejs :url="env('APP_URL'). '/managements/menus/datatable'" />
    function menusCrud() {
        return {
            datatable: datatable(),
            openModal : false,
            formState : 'save',
            loadingState: false,
            idData : null,
            successAlert: {
                open: false,
                message: ''
            },
            failedAlert: {
                open: false,
                message: ''
            },
            form: {
                menugroup_id: '',
                menu_label: '',
                menu_icon: '',
                menu_desc: '',
                menu_order: '',
                action_id: '',
                route: ''
            },
            errMsg: {
                menugroup_id: '',
                menu_label: '',
                menu_icon: '',
                menu_desc: '',
                menu_order: '',
                action_id: '',
                route: ''
            },
            addData() {
                this.resetForm()
                this.idData = null
                this.formState = 'save'
                this.openModal = true
            },
            confirmSave() {
                const title = this.formState == 'edit' ? 'Ubah data?' : 'Simpan data?'
                this.loadingState = true
                
                Swal.fire({
                title: title,
                text: "pastikan data yang diinputkan sudah benar!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        this.saveData()
                    }
                    else this.loadingState = false
                })
            },
            async saveData() {
                    try {
                        const response = this.formState == 'save' ? await axios.post('{{ env('APP_URL') }}/managements/menus', this.form) 
                                                                : await axios.put('{{ env('APP_URL') }}/managements/menus/' + this.idData, this.form)
                        if(response.status == 200) {
                            
                            Swal.fire({
                                icon: 'success',
                                title: response.data.message,
                                showConfirmButton: false,
                                timer: 1500
                            })

                            this.successAlert = {
                                open: true,
                                message: response.data.message
                            }
                            this.openModal = false
                            this.resetForm()
                            this.datatable.refreshTable()
                            this.loadingState = false
                        }
                    } catch (e) {
                        if(e.response.status == 422) {
                            this.loadingState = false
                            Swal.fire({
                                icon: 'error',
                                title: e.response.data.message,
                                showConfirmButton: false,
                                timer: 1500
                            })
                            let errors = e.response.data.errors;
                            Object.keys(this.errMsg).forEach(key => {
                                this.errMsg[key] = Array.isArray(errors[key]) ? errors[key].map((value) => {
                                return value;
                                }).join(' ') : errors[key]
                            });
                            
                        }
                    }
                
            },
            async editData(id = 0) {
                this.resetForm();
                this.idData = id
                this.formState = 'edit'
                this.loadingState = true
                try {
                    const response = await axios.get('{{ env('APP_URL') }}/managements/menus/'+id);
                    if(response.status == 200) {
                        const dataApi = response.data.menu;
                        this.form = {
                            menu_label: dataApi.menu_label,
                            menu_icon: dataApi.menu_icon,
                            menu_desc: dataApi.menu_desc,
                            menu_order: dataApi.menu_order,
                            route: dataApi.route
                        }
                        this.actionsSelect.addItem(dataApi.action_id)
                        this.menugroupSelect.addItem(dataApi.menugroup_id)
                        this.openModal = true
                        this.loadingState = false
                    }
                } catch (e) {
                    this.loadingState = false
                    Swal.fire({
                        icon: 'error',
                        title: "something went wrong",
                        showConfirmButton: false,
                        timer: 1500
                    })
                }
            },
            confirmDelete(id = 0) {
                this.idData = id
                this.loadingState = true
                Swal.fire({
                title: 'Hapus data ini?',
                text: "data yang sudah dihapus tidak dapat dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        this.deleteData()
                    }
                    else this.loadingState = false
                })
            },
            async deleteData() {
                try {
                    const response = await axios.delete('{{ env('APP_URL') }}/managements/menus/'+this.idData);
                    if(response.status == 200) {
                    
                        Swal.fire({
                            icon: 'success',
                            title: response.data.message,
                            showConfirmButton: false,
                            timer: 1500
                        })

                        this.successAlert = {
                            open: true,
                            message: response.data.message
                        }
                        this.datatable.refreshTable()
                        this.loadingState = false
                    }
                } catch (e) {
                    this.loadingState = false
                    Swal.fire({
                        icon: 'error',
                        title: "something went wrong",
                        showConfirmButton: false,
                        timer: 1500
                    })
                }
            },
            resetForm() {
                this.form = {
                    menugroup_id: '',
                    menu_label: '',
                    menu_icon: '',
                    menu_desc: '',
                    menu_order: '',
                    action_id: '',
                    route: ''
                }
                this.errMsg = {
                    menugroup_id: '',
                    menu_label: '',
                    menu_icon: '',
                    menu_desc: '',
                    menu_order: '',
                    action_id: '',
                    route: ''
                }
                this.menugroupSelect.clear()
                this.actionsSelect.clear()
            },
            menugroupSelect: null,
            actionsSelect: null,
            async getMenugroups()
            {
                try {
                    const response = await axios.get('{{ env('APP_URL') }}/managements/menugroups/showall')
                    if(response.status == 200) {
                        console.log(response.data.menugroups);
                        this.menugroupSelect = new TomSelect('#menugroupDom',{
                            create: true,
                            valueField: 'id',
                            labelField: 'menugroup_label',
                            searchField: 'menugroup_label',
                            options: response.data.menugroups,
                            render: {
                                option: function(data, escape) {
                                    return '<div class="flex flex-col mb-2">' +
                                                '<span class="px-2 py-1 text-sm font-bold">' + escape(data.menugroup_label) + '</span>' +
                                                '<span class="px-2 text-xs">' + escape(data.menugroup_desc) + '</span>' +
                                            '</div>';
                                },
                                item: function(data, escape) {
                                    return '<div title="' + escape(data.menugroup_desc) + '" class="text-base font-bold" >' + escape(data.menugroup_label) + '</div>';
                                }
                            }
                        }) 
                    }
                } catch (e) {
                    console.log(e);
                    Swal.fire({
                        icon: 'error',
                        title: "failed to get menugroups",
                        showConfirmButton: false,
                        timer: 1500
                    })
                }
            },
            async getActions()
            {
                try {
                    const response = await axios.get('{{ env('APP_URL') }}/managements/actions/showall/nonajaxonly')
                    if(response.status == 200) {
                        this.actionsSelect = new TomSelect('#actionDom',{
                            create: true,
                            valueField: 'id',
                            labelField: 'action_path',
                            searchField: 'action_path',
                            options: response.data.actions,
                            render: {
                                option: function(data, escape) {
                                    return '<div class="flex flex-col mb-2">' +
                                                '<span class="px-2 py-1 text-sm font-bold">' + escape(data.action_path) + '</span>' +
                                            '</div>';
                                },
                                item: function(data, escape) {
                                    return '<div title="' + escape(data.action_path) + '" class="text-base font-bold" >' + escape(data.action_path) + '</div>';
                                }
                            }
                        })
                    }
                } catch (e) {
                    console.log(e);
                    Swal.fire({
                        icon: 'error',
                        title: "failed to get actions",
                        showConfirmButton: false,
                        timer: 1500
                    })
                }
            }

        }
    }
</script>