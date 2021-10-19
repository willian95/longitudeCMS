<script>
        
    const app = new Vue({
        el: '#dev-product-list',
        data(){
            return{
                services:[],
                pages:0,
                page:1,
                loading:false,
                links:[],
                currentPage:"",
                totalPages:"",

                linkClass:"page-link",
                activeLinkClass:"page-link active-link",
            }
        },
        methods:{
            
            fetch(page){
                
                let url = ""
                if(page)
                    url = page.url
                else
                    url = "{{ route('services.fetch') }}"

                axios.get(url)
                .then(res => {
                    
                    this.links = res.data.links
                    this.currentPage = res.data.current_page
                    this.totalPages = res.data.last_page

                    this.services = res.data.data
                    
                })
                

            },


        },
        mounted(){
            
            this.fetch()

        }

    })

</script>