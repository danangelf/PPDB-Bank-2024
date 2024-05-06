<script>
    
    <x-app.datatable.datatablejs :url="env('APP_URL'). '/data/sekolah/datatable'" />
    function dataCrud() {
        return {
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
                kode_kabupaten: '',
                kabupaten: '',
                kode_kecamatan: '',
                kecamatan: '',
            },
            errMsg: {
                kode_kabupaten: '',
                kabupaten: '',
                kode_kecamatan: '',
                kecamatan: '',
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
                        const response = this.formState == 'save' ? await axios.post('{{ env('APP_URL') }}/data/sekolah', this.form) 
                                                                : await axios.put('{{ env('APP_URL') }}/data/sekolah/' + this.idData, this.form)
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
                        this.loadingState = false
                        if(e.response.status == 422) {
                            console.log(e.response);
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
                    const response = await axios.get('{{ env('APP_URL') }}/data/sekolah/'+id);
                    if(response.status == 200) {
                        const dataApi = response.data.data;
                        this.form = {
                            kode_kabupaten: dataApi.kode_kabupaten,
                            kabupaten: dataApi.kabupaten,
                            kode_kecamatan: dataApi.kode_kecamatan,
                            kecamatan: dataApi.kecamatan,
                        }
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
                    const response = await axios.delete('{{ env('APP_URL') }}/data/sekolah/'+this.idData);
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
                    kode_kabupaten: '',
                    kabupaten: '',
                    kode_kecamatan: '',
                    kecamatan: '',
                }
                this.errMsg = {
                    kode_kabupaten: '',
                    kabupaten: '',
                    kode_kecamatan: '',
                    kecamatan: '',
                }
            },
            async synchData() {
                this.loadingState = true
                try {
                    if(this.selected.kecamatan === ""){
                        Swal.fire({
                            icon: 'warning',
                            title: "Silakan pilih kecamatan terlebih dahulu",
                            showConfirmButton: false,
                            timer: 1500
                        })
                        this.loadingState = false
                        return false
                    }
                    const response = await axios.get('{{ env('APP_URL') }}/data/sekolah/synch/' + this.selected.kecamatan);
                    if(response.status == 200) {
                        Swal.fire({
                            icon: 'success',
                            title: response.data.message,
                            showConfirmButton: false,
                            timer: 1500
                        })
                        this.loadingState = false
                        this.datatable.refreshTable()
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
            kabkotaSelect: null,
            kecamatanSelect: null,
            kabkotaData: [],
            kecamatanData: [],
            selected: {
                kabkota: '',
                kecamatan: '',
            },
            async getKecamatan()
            {
                try {
                    const response = await axios.get('{{ env('APP_URL') }}/data/kecamatan/showall')
                    if(response.status == 200) {
                        this.kabkotaData = response.data.data.kabkota
                        this.kabkotaData.unshift({kode_kabupaten: '', kabupaten: 'Semua Kabupaten'})
                        this.kabkotaSelect = new TomSelect('#kabkotaDom',{
                            valueField: 'kode_kabupaten',
                            labelField: 'kabupaten',
                            searchField: 'kabupaten',
                            options: this.kabkotaData,
                            render: {
                                option: function(data, escape) {
                                    return '<div class="flex flex-col mb-2">' +
                                                '<span class="px-2 py-1 text-sm font-bold">' + escape(data.kabupaten) + '</span>' +
                                                '<span class="px-2 text-xs">' + escape(data.kode_kabupaten) + '</span>' +
                                            '</div>';
                                },
                                item: function(data, escape) {
                                    return '<div title="' + escape(data.kode_kabupaten) + '" class="text-sm font-bold" >' + escape(data.kabupaten) + '</div>';
                                }
                            }
                        }) 
                        this.kecamatanData = response.data.data.kecamatan
                        
                        this.initSelectKecamatan()
                    }
                } catch (e) {
                    console.log(e);
                    Swal.fire({
                        icon: 'error',
                        title: "failed to get kecamatan",
                        showConfirmButton: false,
                        timer: 1500
                    })
                }
            },
            initSelectKecamatan()
            {
                this.kecamatanSelect = new TomSelect('#kecamatanDom',{
                    valueField: 'kode_kecamatan',
                    labelField: 'kecamatan',
                    searchField: 'kecamatan',
                    options: this.filteredKecamatan(),
                    render: {
                        option: function(data, escape) {
                            return '<div class="flex flex-col mb-2">' +
                                        '<span class="px-2 py-1 text-sm font-bold">' + escape(data.kecamatan) + '</span>' +
                                        '<span class="px-2 text-xs">' + escape(data.kabupaten) + '</span>' +
                                    '</div>';
                        },
                        item: function(data, escape) {
                            return '<div title="' + escape(data.kabupaten) + '" class="text-sm font-bold" >' + escape(data.kecamatan) + '</div>';
                        }
                    }
                }) 
            },
            filteredKecamatan()
            {
                const filtered =  this.selected.kabkota == '' ? this.kecamatanData : this.kecamatanData.filter(item => item.kode_kabupaten == this.selected.kabkota)
                filtered.unshift({kode_kecamatan: '', kecamatan: 'Semua Kecamatan', kabupaten: "Semua Kecamatan"})
                return filtered
            },
            onChangeKabkota()
            {
                this.kecamatanSelect.clear()
                this.kecamatanSelect.clearOptions()
                this.kecamatanSelect.addOptions(this.filteredKecamatan(), user_created=false)
            },
            datatable: datatable()
        }
    }
</script>