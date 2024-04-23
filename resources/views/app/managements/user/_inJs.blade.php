<script>
    
    <x-app.datatable.datatablejs :url="env('APP_URL'). '/managements/user/datatable'" />
    function userCrud() {
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
                name: '',
                username: '',
                roles: [],
                is_active: true,
                password: '',
                password_confirmation: ''
            },
            errMsg: {
                name: '',
                username: '',
                roles: '',
                is_active: '',
                password: '',
                password_confirmation: ''
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
                        const response = this.formState == 'save' ? await axios.post('{{ env('APP_URL') }}/managements/user', this.form) 
                                                                : await axios.put('{{ env('APP_URL') }}/managements/user/' + this.idData, this.form)
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
                                if(key !== "password_confirmation")
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
                    const response = await axios.get('{{ env('APP_URL') }}/managements/user/'+id);
                    if(response.status == 200) {
                        const user = response.data.user;
                        this.form = {
                            name: user.name,
                            username: user.username,
                            is_active: user.is_active.toString(),
                            password: '',
                            password_confirmation: ''
                        }
                        user.roleplay.forEach(role => {
                            this.rolesDomSelect.addItem(role.role_id)
                        });
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
            roleOptions: [],
            rolesDomSelect: null,
            async getRoles()
            {
                try {
                    const response = await axios.get('{{ env('APP_URL') }}/managements/roles/showall')
                    if(response.status == 200) {
                        this.roleOptions = response.data.roles
                        this.rolesDomSelect = new TomSelect('#roleSelect', {
                            valueField: 'id',
                            labelField: 'role_name',
                            searchField: 'role_name',
                            options: this.roleOptions,
                            render: {
                                option: function(data, escape) {
                                    return '<div class="flex flex-col mb-2">' +
                                                '<span class="px-2 py-1 text-sm font-bold">' + escape(data.role_name) + '</span>' +
                                                '<span class="px-2 text-xs">' + escape(data.role_desc) + '</span>' +
                                            '</div>';
                                },
                                item: function(data, escape) {
                                    return '<div title="' + escape(data.role_desc) + '" class="text-base font-bold" >' + escape(data.role_name) + '</div>';
                                }
                            }
                        
                        })
                    }
                } catch (e) {
                    console.log(e)
                    Swal.fire({
                        icon: 'error',
                        title: "Roles failed to load",
                        showConfirmButton: false,
                        timer: 1500
                    })
                }
            },
            async deleteData() {
                try {
                    const response = await axios.delete('{{ env('APP_URL') }}/managements/user/'+this.idData);
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
                this.rolesDomSelect.clear()
                this.form = {
                    name: '',
                    username: '',
                    roles: [],
                    is_active: true,
                    password: '',
                    password_confirmation: ''
                }
                this.errMsg = {
                    name: '',
                    username: '',
                    roles:'',
                    is_active: '',
                    password: '',
                    password_confirmation: ''
                }
            }
        }
    }
</script>