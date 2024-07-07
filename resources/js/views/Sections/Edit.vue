<template>
    <b-modal ref="my-modal" :title="modal_title" @hidden="$emit('modalClose')" size="lg"  scrollable no-close-on-backdrop no-fade static id="mymodal">
        <div slot="modal-footer">
            <b-button variant="primary" @click="$refs['dummy_submit'].click()" :disabled="isLoading">{{ __('save') }}
                <b-spinner v-if="isLoading" small label="Spinning"></b-spinner>
            </b-button>
            <b-button variant="secondary" @click="hideModal">{{ __('cancel') }}</b-button>
        </div>
        <form ref="my-form" @submit.prevent="saveRecord">
            <div class="row">
                <div class="form-group">
                    <label for='title'>{{ __('title_for_section') }}</label>
                    <input type='text' name='title' id='title' v-model="section.title" class='form-control' placeholder='Ex : Featured Products / Products on Sale' required />
                </div>
                <div class="form-group">
                    <label for='short_description'>{{ __('short_descrption') }}</label>
                    <input type='text' name='short_description' id='short_description' v-model="section.short_description" class='form-control' placeholder='Ex : Weekends deal goes here' required />
                </div>
<!--                <div class="form-group">
                    <label for='style'>Section Style</label>
                    <select name='style' id='style' v-model="section.style" class='form-control form-select'>
                        <option value="">Select Style</option>
                        <option value="style_1">Style 1</option>
                        <option value="style_2">Style 2</option>
                        <option value="style_3">Style 3</option>
                    </select>
                </div>-->
                <div class="form-group">
                    <label>{{ __('category_ids_with_ex') }}</label>
<!--                    <multiselect v-model="section.categories_ids"
                                 id="category"
                                 tag-placeholder="Add this as new category"
                                 placeholder="Search or Select a category"
                                 label="name"
                                 track-by="name"
                                 :options="categories"
                                 :multiple="true"
                                 :taggable="true"
                                 @tag="addTag">
                    </multiselect>-->
                    <Select2 v-model="section.category_ids"
                             placeholder="Select Categories"
                             :options="categories_options"
                             :settings="{ multiple: 'multiple', width:'100%',dropdownParent:'#mymodal' }" />
                </div>
                <div class="form-group">
                    <label for='product_type'> {{ __('product_types') }}</label>
                    <select name='product_type' id='product_type' v-model="section.product_type"  class='form-control form-select'>
                        <option value=""> {{ __('select_product_type') }}</option>
                        <option value="all_products"> {{ __('all_products') }}</option>
                        <option value="new_added_products"> {{ __('new_added_products') }}</option>
                        <option value="products_on_sale"> {{ __('products_on_sale') }}</option>
                        <option value="most_selling_products">{{ __('most_selling_products') }}</option>
                        <option value="custom_products"> {{ __('custom_products') }}</option>
                    </select>
                </div>
                <div class="form-group" v-if="section.product_type === 'custom_products'" >
                    <label >{{ __('products') }}</label>
<!--                    <multiselect v-model="section.product_ids"
                                 id="product"
                                 tag-placeholder="Add this as new product"
                                 placeholder="Search or Select a product"
                                 label="name"
                                 track-by="name"
                                 :options="products"
                                 :multiple="true"
                                 :taggable="true"
                                 @tag="addTag">
                    </multiselect>-->
                    <Select2 v-model="section.product_ids"
                             placeholder="Select Products"
                             :options="products_options"
                             :settings="{ multiple: 'multiple', width:'100%',dropdownParent:'#mymodal' }" />

                </div>
            </div>
            <button ref="dummy_submit" style="display:none;"></button>
        </form>
    </b-modal>
</template>

<script>

import axios from 'axios';
import Multiselect from 'vue-multiselect'
import Select2 from "v-select2-component";
export default {
    props: ['record'],
    components: {
        Multiselect,
        Select2
    },
    data : function(){
        return {
            isLoading: false,
            categories:[],
            products:[],
            category_id: '',
            section : {
                id: this.record ? this.record.id : null,
                title: this.record ? this.record.title : "",
                short_description: this.record ? this.record.short_description : "",
                /*style: this.record ? this.record.style : "",*/
                category_ids: (this.record && this.record.category_ids) ? this.record.category_ids.split(",") : "",
                product_type: this.record ? this.record.product_type : "",
                product_ids: (this.record && this.record.product_ids) ? this.record.product_ids.split(",") : "",
            },
        };
    },
    created: function () {
        this.getCategories();
        this.getProducts();
    },

    computed: {
        modal_title: function(){
            let title = this.section.id ?  __('edit') : __('create') ;
            title += __('manage_featured_products_section');
            return title;
        },
         categories_options: function(){
//             function buildCategoryTree(categories, temp, indent = 0, disabled=false) {
//   categories.forEach(category => {
//     const text = '-'.repeat(indent) + category.name;
//      if(category.cat_active_childs.length !== 0){
//     temp.push({ id: category.id, text: text,  disabled:true});
//     }else{
//         temp.push({ id: category.id, text: text,  disabled:false}); 
//     }
    
//     if (category.cat_active_childs && category.cat_active_childs.length !== 0) {
//       buildCategoryTree(category.cat_active_childs, temp, indent + 1, disabled=false);
//     }
//   });
// }

// const temp = [];
// if (this.categories.length !== 0) {
//   buildCategoryTree(this.categories, temp);
// }

// console.log(temp);
// return temp;
var temp = [];
            if(this.categories.length !== 0 ){
                this.categories.forEach(category => {
                    //Only Main Categories
                    if (category.parent_id == 0) {
                        if(category.cat_active_childs.length !== 0){
                            temp.push({id: category.id, text: category.name, disabled:true});
                            category.cat_active_childs.forEach(subcat => {
                                if(subcat.cat_active_childs.length !== 0){
                                    temp.push({id: subcat.id, text: '-'+subcat.name, disabled:true});
                                }else{
                                    temp.push({id: subcat.id, text: '-'+subcat.name, disabled:false});
                                }
                                subcat.cat_active_childs.forEach(subcat2 => {
                                    if(subcat2.cat_active_childs.length !== 0){
                                        temp.push({id: subcat2.id, text: '--'+subcat2.name, disabled:true});
                                    }else{
                                        temp.push({id: subcat2.id, text: '--'+subcat2.name, disabled:false});
                                    }
                                    subcat2.cat_active_childs.forEach(subcat3 => {
                                        if(subcat3.cat_active_childs.length !== 0){
                                            temp.push({id: subcat3.id, text: '---'+subcat3.name, disabled:true});
                                        }else{
                                            temp.push({id: subcat3.id, text: '---'+subcat3.name, disabled:false});
                                        }
                                        subcat3.cat_active_childs.forEach(subcat4 => {
                                            if(subcat4.cat_active_childs.length !== 0){
                                                temp.push({id: subcat4.id, text: '----'+subcat4.name, disabled:true});
                                            }else{
                                                temp.push({id: subcat4.id, text: '----'+subcat4.name, disabled:false});
                                            }
                                            subcat4.cat_active_childs.forEach(subcat5 => {
                                                if(subcat5.cat_active_childs.length !== 0){
                                                    temp.push({id: subcat5.id, text: '-----'+subcat5.name, disabled:true});
                                                }else{
                                                    temp.push({id: subcat5.id, text: '-----'+subcat5.name, disabled:false});
                                                }
                                             });
                                        });
                                    });
                                });
                            });
                        }
                        else{
                          temp.push({id: category.id, text: category.name, disabled:false});  
                        }
                    }
                });
            }
           
            console.log(temp);
             return temp;
        },
        products_options: function(){
            var temp = [];
            if(this.products.length !== 0 ) {
                this.products.forEach(product => {
                    temp.push({id: product.id, text: product.name})
                });
            }
            return temp;
        },
    },

    methods: {
        showModal() {
            this.$refs['my-modal'].show()
        },
        hideModal() {
            this.$refs['my-modal'].hide()
        },
        getCategories(){
            this.isLoading = true
            axios.get(this.$apiUrl + '/categories/active')
                .then((response) => {
                    this.isLoading = false
                    this.categories = response.data.data;
                }).catch(error => {
                this.isLoading = false;
                if (error.request.statusText) {
                    this.showError(error.request.statusText);
                }else if (error.message) {
                    this.showError(error.message);
                } else {
                    this.showError(__('something_went_wrong'));
                }
            });
        },
        getProducts(){
            this.isLoading = true
            axios.get(this.$apiUrl + '/products/active')
                .then((response) => {
                    this.isLoading = false
                    this.products = response.data.data;
                }).catch(error => {
                this.isLoading = false;
                if (error.request.statusText) {
                    this.showError(error.request.statusText);
                }else if (error.message) {
                    this.showError(error.message);
                } else {
                    this.showError(__('something_went_wrong'));
                }
            });
        },
        saveRecord: function(){



            let vm = this;
            this.isLoading = true;
            let formData = new FormData();

            /*if(this.section.id) {
                formData.append('id', this.section.id);
              }*/
            let data = this.section;
            for(let key in data){
                if (data[key]!== null)
                {
                    /*if (key == 'category_ids' || key == 'product_ids' ){
                        formData.append(key, JSON.stringify(data[key]));
                    }else{
                        formData.append(key, data[key]);
                    }*/
                        formData.append(key, data[key]);
                }
            }



            let url = this.$apiUrl + '/sections/save';
            if(this.section.id){
                url = this.$apiUrl + '/sections/update';
            }
            axios.post(url, formData).then(res => {
                let data = res.data;
                if (data.status === 1) {
                    this.$eventBus.$emit('sectionSaved', data.message);
                    vm.$router.push({path: '/sections'});
                    this.hideModal();
                }else{
                    vm.showError(data.message);
                    vm.isLoading = false;
                }
            }).catch(error => {
                vm.isLoading = false;
                if (error.request.statusText) {
                    this.showError(error.request.statusText);
                }else if (error.message) {
                    this.showError(error.message);
                } else {
                    this.showError(__('something_went_wrong'));
                }
            });
        },
        addCategoryTag (newTag) {
            console.log('newTag : ', newTag)
            const tag = {
                name: newTag,
            }
            this.categories_ids.push(tag)
        },
    },
    mounted(){
        this.showModal();
    }
}
</script>

<style scoped>
/*@import "../../../../node_modules/vue-multiselect/dist/vue-multiselect.min.css";*/
/*.image_preview{
    margin-top: 5px;
}*/
/*.select2-container--default .select2-selection--multiple .select2-selection__rendered .select2-search__field input {
    width: 5000px !important;
}*/

.select2-search__field input[type=search] {
    width: 5000px !important;
}
</style>
