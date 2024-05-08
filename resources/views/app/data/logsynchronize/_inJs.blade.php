<script>
    
    <x-app.datatable.datatablejs :url="env('APP_URL'). '/data/logsynchronize/datatable'" />
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
                table: '',
                params: '',
                status_result: '',
                result: '',
                msg_result: '',
                extra_info: '',
                created_by: '',
                created_at: '',
            },
            async editData(id = 0) {
                this.resetForm();
                this.idData = id
                this.formState = 'edit'
                this.loadingState = true
                try {
                    const response = await axios.get('{{ env('APP_URL') }}/data/logsynchronize/'+id);
                    if(response.status == 200) {
                        const dataApi = response.data.data;
                        this.form = {
                            table: dataApi.table,
                            params: dataApi.params,
                            status_result: dataApi.status_result,
                            result: dataApi.result,
                            msg_result: dataApi.msg_result,
                            extra_info: dataApi.extra_info,
                            created_by: dataApi.created_by,
                            created_at: dataApi.created_at,
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
            datatable: datatable()
        }
    }
</script>