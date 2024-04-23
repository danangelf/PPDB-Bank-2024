@props([
    'url'
])
    function datatable(){
        return {
            loading: false,
            data: [],
            search: "",
            pagination: {
                page: 1,
                per_page: 10,
                total_page: 0,
                total_records: 0,
            },
            nearby_pages: 3,
            pages: [],
            previousPage(){
                if(this.pagination.page > 1){
                    this.pagination.page--
                    this.getData()
                    this.updatePages()
                }
            },
            nextPage(){
                if(this.pagination.page < this.pagination.total_page){
                    this.pagination.page++
                    this.getData()
                    this.updatePages()
                }
            },
            goToPage(p){
                if(Number.isInteger(p)){
                    this.pagination.page = p
                    this.getData()
                    this.updatePages()
                }
            },
            updatePages() {
                this.pages = [];

                if (this.pagination.total_page <= 5) {
                    for (let page = 1; page <= this.pagination.total_page; page++) {
                        this.pages.push(page);
                    }
                } else {
                    const startPage = Math.max(this.pagination.page - this.nearby_pages, 1);
                    const endPage = Math.min(parseInt(this.pagination.page) + this.nearby_pages, this.pagination.total_page);

                    if (startPage > 1) {
                        this.pages.push(1);
                        this.pages.push('...');
                    }

                    for (let page = startPage; page <= endPage; page++) {
                        this.pages.push(page);
                    }

                    if (endPage < this.pagination.total_page) {
                        this.pages.push('...');
                        this.pages.push(this.pagination.total_page);
                    }
                }
            },
            isEmpty(){
                return this.data.length > 0 ? false : true
            },
            async getData(){
                this.loading = true
                try {
                    const params = {
                        page: this.pagination.page,
                        per_page: this.pagination.per_page,
                        search: this.search
                    }
                    const response = await axios.get("{{ $url }}", { params })
                    if(response.status == 200) {
                        this.data = response.data.data
                        this.pagination = response.data.pagination
                        this.updatePages()
                    }
                    this.loading = false
                } catch (error) {
                    this.data = []
                    this.loading = false
                }
            },
            isCurrentPage(p){
                return parseInt(this.pagination.page) === parseInt(p)
            },
            refreshTable(){
                this.pagination.page = 1
                this.getData()
            },
            numbering(index){
                let no = parseInt(index) + 1
                if(this.pagination.page > 1){  
                    no = no + (this.pagination.per_page * (this.pagination.page - 1))
                }
                return no
            },
            showingLabel(){
                let starts = 1
                let ends = this.pagination.per_page

                if(parseInt(this.pagination.page) > 1){
                    starts = (parseInt(this.pagination.per_page) * (this.pagination.page - 1)) + 1
                    ends = parseInt(this.pagination.per_page) * parseInt(this.pagination.page)
                }
                

                ends = ends > parseInt(this.pagination.total_records) ? parseInt(this.pagination.total_records) : ends

                return starts + ' - ' + ends
            }
        }
    }