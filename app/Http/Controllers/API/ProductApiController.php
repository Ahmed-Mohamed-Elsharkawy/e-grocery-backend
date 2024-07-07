<?php

namespace App\Http\Controllers\API;

use App\Helpers\CommonHelper;
use App\Http\Controllers\Controller;
use App\Imports\ProductsImport;
use App\Models\Seller;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImages;
use App\Models\ProductVariant;
use App\Models\Setting;
use App\Models\Tax;
use App\Models\Unit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;
use Response;
use Illuminate\Support\Str;


class ProductApiController extends Controller
{
    /*public function index(){

        //$products = Product::with('seller')->orderBy('id','DESC')->get();

        $join = "LEFT JOIN `product_variants` pv ON pv.product_id = p.id
            LEFT JOIN `units` u ON u.id = pv.measurement_unit_id
            LEFT JOIN `sellers` s ON s.id = p.seller_id";

        $where = '';
        $products  = \DB::select(\DB::raw("SELECT p.id AS product_id,p.*, p.name,p.seller_id,p.status,p.tax_id, p.image,
        s.name as seller_name, p.indicator, p.manufacturer, p.made_in, p.return_status, p.cancelable_status, p.till_status,p.description,
        pv.id as product_variant_id, pv.price, pv.discounted_price, pv.measurement, pv.status, pv.stock,pv.stock_unit_id, u.short_code,
        (select short_code from units where units.id = pv.stock_unit_id) as stock_unit
        FROM `products` p $join $where "));

        return CommonHelper::responseWithData($products);
    }*/

    public function getProducts(Request $request){ 
        $limit = ($request->limit);
        $offset = ($request->offset);
        if(!isset($request->type)){
            $sellers = Seller::orderBy('id','DESC')->get()->toArray();
        }
        $categories = Category::orderBy('id','DESC')->get()->toArray();

        $join = "LEFT JOIN `categories` c ON c.id = p.category_id
        LEFT JOIN `product_variants` pv ON pv.product_id = p.id
            LEFT JOIN `units` u ON u.id = pv.stock_unit_id
            LEFT JOIN `sellers` s ON s.id = p.seller_id
            LEFT JOIN `order_status_lists` osl ON osl.id = p.till_status
            ";
        $where = '';

        /*if(isset($request->shipping_type) && $request->shipping_type !== "" ){
            $where .= empty($where) ? " WHERE p.standard_shipping = $request->shipping_type" : " AND p.standard_shipping = $request->shipping_type";
        }*/

        if(isset($request->is_approved) && $request->is_approved !== "" ){
            $where .= empty($where) ? " WHERE p.is_approved = $request->is_approved" : " AND p.is_approved = $request->is_approved";
        }

        if(isset($request->seller) && $request->seller !== "" ){
            $where .= empty($where) ? " WHERE p.seller_id = $request->seller" : " AND p.seller_id = $request->seller";
        }

        if(isset($request->category) && $request->category !== "" ){
            $where .= empty($where) ? " WHERE p.category_id = $request->category" : " AND p.category_id = $request->category";
        }
        //here packet paroducts
        if(isset($request->type) && $request->type === 'packet_products'){
            $where .= empty($where) ? " WHERE p.type = 'packet'" : " AND p.type ='packet'";
        }
        //here loose paroducts
        if(isset($request->type) && $request->type === 'loose_products'){
            $where .= empty($where) ? " WHERE p.type = 'loose'" : " AND p.type ='loose'";
        }
        //here Sold Out as 0
        if(isset($request->type) && $request->type === 'sold_out'){
            $where .= empty($where) ? " WHERE (pv.stock <=0 OR pv.status = '0') AND p.is_unlimited_stock = 0" : " AND (pv.stock <=0 OR pv.status = '0') AND p.is_unlimited_stock = 0";
        }
        //here Available as 1, low_stock_limit
        if(isset($request->type) && $request->type === 'low_stock'){
            $low_stock_limit = Setting::where('variable', 'low_stock_limit')->first();
            $where .= empty($where) ? " WHERE pv.stock <= ".$low_stock_limit['value']." AND pv.status = '1' AND p.is_unlimited_stock != '1'" : " AND pv.stock <= ".$low_stock_limit['value']." AND pv.status = '1' AND p.is_unlimited_stock != '1'";
        }

        if(isset($request->limit)){

        $products  = \DB::select(\DB::raw("SELECT p.id AS product_id,p.*, p.name,p.seller_id,p.status,p.tax_id, p.image,
        s.name as seller_name, p.indicator, p.manufacturer, p.made_in, p.return_status, p.cancelable_status, p.till_status, osl.status as till_status_name ,p.description,
        pv.id as product_variant_id, pv.price, pv.discounted_price, pv.measurement, pv.status as pv_status , pv.stock,pv.stock_unit_id, u.short_code,
        (select short_code from units where units.id = pv.stock_unit_id) as stock_unit
        FROM `products` p $join $where  order by p.id desc, pv.id asc  LIMIT $limit OFFSET $offset"));
        }else{
            $products  = \DB::select(\DB::raw("SELECT p.id AS product_id,p.*, p.name,p.seller_id,p.status,p.tax_id, p.image,
            s.name as seller_name, p.indicator, p.manufacturer, p.made_in, p.return_status, p.cancelable_status, p.till_status, osl.status as till_status_name ,p.description,
            pv.id as product_variant_id, pv.price, pv.discounted_price, pv.measurement, pv.status as pv_status , pv.stock,pv.stock_unit_id, u.short_code,
            (select short_code from units where units.id = pv.stock_unit_id) as stock_unit
            FROM `products` p $join $where  order by p.id desc, pv.id asc"));
        }
        $total = count($products);
        $data = array(
            "categories" => $categories,
            "products" => $products,
         
        );
        if(!isset($request->type)){
            $data["sellers"] = $sellers;
        }
       
        return CommonHelper::responseWithData($data, $total);
    }

    public function getProducts_sellerapp(Request $request)
    {
    
      
        /*$request->city_id = 1;
        $request->latitude = 23.2419997;
        $request->longitude = 69.6669324;*/

        // $validator = Validator::make($request->all(), [
        //     'latitude' => 'required',
        //     'longitude' => 'required',
        // ], [
        //     'latitude.required' => 'The latitude field is required.',
        //     'longitude.required' => 'The longitude field is required.'
        // ]);
        // if ($validator->fails()) {
        //     return CommonHelper::responseError($validator->errors()->first());
        // }

        try {

            $currency = Setting::get_value('currency');
            $user_id = $request->user('api-customers') ? $request->user('api-customers')->id : '';

            $limit = ($request->limit) ?? 10;
            $offset = ($request->offset) ?? 0;

            $sort = ($request->sort) ?? 'row_order';
            $order = ($request->order) ?? 'asc';

            if ($sort == 'new') {
                $sort = 'created_at DESC';
                $price = 'MIN(discounted_price)';
                $price_sort = 'pv.discounted_price  ASC';
            } elseif ($sort == 'old') {
                $sort = 'created_at ASC';
                $price = 'MIN(discounted_price)';
                $price_sort = 'pv.discounted_price  ASC';
            } elseif ($sort == 'high') {
                //$sort = 'price DESC';

                $sort = 'max_price DESC';

                $price = 'MAX(if(pv.discounted_price > 0 && pv.discounted_price != 0, pv.discounted_price, pv.price))';
                $price_sort = 'if(pv.discounted_price > 0 && pv.discounted_price != 0, pv.discounted_price, pv.price) DESC';
            } elseif ($sort == 'low') {
                // $sort = 'price ASC';

                $sort = 'min_price ASC';

                $price = 'MIN(if(pv.discounted_price > 0 && pv.discounted_price != 0, pv.discounted_price, pv.price))';
                $price_sort = 'if(pv.discounted_price > 0 && pv.discounted_price != 0, pv.discounted_price, pv.price) ASC';
            } elseif ($sort == 'discount') {
                $sort = 'cal_discount_percentage DESC';
                $price = 'MIN(if(pv.discounted_price > 0 && pv.discounted_price != 0, pv.discounted_price, pv.price))';
                $price_sort = 'cal_discount_percentage DESC';
            } elseif ($sort == 'popular') {
                $sort = 'order_counter DESC';
                $price = 'MIN(pv.discounted_price)';
                $price_sort = 'order_counter DESC';
            } else {
                $sort = 'p.row_order ASC';
                $price = 'MIN(pv.discounted_price)';
                $price_sort = 'pv.id  ASC';
            }

            // ->orderByRaw($sort)

            //$product_id = $request->get('product_id');

            $category_id = $request->get('category_id');


            $seller_id = auth()->user()->seller->id;;
            $brand_id = $request->get('brand_id');
            $seller_slug = '';
            $where = "";

            if (isset($request['search']) && $request['search'] != '') {
                $search = $request['search'];
//                $where .= " AND (p.`id` like '%" . $search . "%' OR p.`name` like '%" . $search . "%' OR s.`name` like '%" . $search . "%' OR p.`category_id` like '%" . $search . "%' OR p.`slug` like '%" . $search . "%' OR p.`description` like '%" . $search . "%') ";
                $where .= " AND ( p.`name` like '%" . $search . "%' OR p.`slug` like '%" . $search . "%' OR p.`tags` like '%" . $search . "%') ";
            }

            if (isset($request->section_id) && $request->section_id != "") {
                $section_id = $request->section_id;
                $section = Section::select("*")->where("id", "=", $section_id)->first();

                $product_ids = CommonHelper::getProductIdsSection($section);
                if ($product_ids !== "") {
                    $where .= "AND p.id IN  ($product_ids)";
                }
            }

            /*if (isset($request['product_id']) && !empty($request['product_id']) && is_numeric($request['product_id'])) {
                $where .= " AND p.`id` = " . $product_id;
            }*/

            if (isset($request['seller_slug']) && !empty($request['seller_slug'])) {
                $seller_slug = $request['seller_slug'];
                if (isset($request['category_id']) && !empty($request['category_id']) && is_numeric($request['category_id'])) {
                    $seller_category = Seller::where('slug', $seller_slug)->first(['categories']);
                    if(!empty($seller_category)) {
                        $category = $seller_category['categories'];
                        $data = explode(",", $category);
                        $search = (in_array($category_id, $data, TRUE)) ? 1 : 2;
                        if ($search == 2) {
                            return CommonHelper::responseError(__('no_products_found'));
                        } else {
                            $where .= " AND s.`slug` = '$seller_slug' AND p.`category_id` IN (" . $category_id . ") ";
                        }
                    }else{
                        return CommonHelper::responseError(__('no_products_found'));
                    }
                } else {
                    $seller_category = Seller::where('slug', $seller_slug)->first(['categories']);
                    if(!empty($seller_category)) {
                        $category = $seller_category['categories'];
                        $where .= " AND s.`slug` =  '$seller_slug' AND p.category_id IN (" . $category . " )";
                    }else{
                        return CommonHelper::responseError(__('no_products_found'));
                    }
                }
            }

            if (isset($request['slug']) && !empty($request['slug'])) {
                $slug = $request['slug'];
                $where .= " AND p.`slug` =  '$slug' ";
            }

            if (isset($seller_id) && !empty($seller_id) && is_numeric($seller_id)) {
                
                if (isset($request['category_id']) && !empty($request['category_id']) && is_numeric($request['category_id'])) {
                    
                    $seller_category = Seller::where('id', $seller_id)->first(['categories']);
                    if(!empty($seller_category)) {
                        $category = $seller_category['categories'];
                        $data = explode(",", $category);
                        $search = (in_array($category_id, $data, TRUE)) ? 1 : 2;
                        if ($search == 2) {
                            return CommonHelper::responseError(__('no_products_found'));
                        } else {
                            $where .= " AND p.`seller_id` = " . $seller_id . " AND p.`category_id` IN (" . $category_id . ") ";
                        }
                    }else{
                        return CommonHelper::responseError(__('no_products_found'));
                    }
                } else {
                    
                    $seller_category = Seller::where('id', $seller_id)->first(['categories']);
                    if(!empty($seller_category)) {
                        $category = $seller_category['categories'];
                        $where .= " AND p.`seller_id` = " . $seller_id . " AND p.category_id IN (" . $category . " )";
                    }else{
                        return CommonHelper::responseError(__('no_products_found'));
                    }
                }
            }

            if (isset($request['category_id']) && !empty($request['category_id']) && is_numeric($request['category_id'])) {
                if (!isset($seller_id) && empty($seller_id) && !isset($request['seller_slug']) && empty($request['seller_slug'])) {
                    $where .= " AND p.`category_id`=" . $category_id;
                }
            }


            if (isset($request['category_id']) && !empty($request['category_id']) && is_numeric($request['category_id'])) {
                $where .= " AND p.`category_id`=" . $category_id;
            }

            if (isset($request['brand_id']) && !empty($request['brand_id']) && is_numeric($request['brand_id'])) {
                $where .= " AND p.`brand_id`=" . $brand_id;
            }




           // $seller_ids = CommonHelper::getSellerIds($request->latitude, $request->longitude);
            $seller_id =  $seller_id;

       
          

            $products = array();
            $i = 0;
       

           
             $products = Product::select('p.*','p.type as d_type', 's.store_name as seller_name','s.slug as seller_slug','s.status as seller_status')
                ->from('products as p')
                ->leftJoin('sellers as s', 'p.seller_id', '=', 's.id')
                ->leftJoin('categories as c', 'p.category_id', '=', 'c.id')
                // ->where('p.is_approved',1)
                // ->where('p.status',1)
                // ->where('c.status',1)
                // /*->where('s.categories', 'like', DB::raw('CONCAT("%", p.category_id ,"%")'))*/
                ->where('s.status',1)
                ->where('p.seller_id',$seller_id)
                
             ->groupBy("p.id");
                
                 if (isset($request->min_price) && isset($request->max_price) && intval($request->max_price)) {
                $products = $products->havingRaw(" min_price > " . intval(intval($request->min_price) - 1) . " and max_price < " . intval(intval($request->max_price) + 1));
            }

            if (isset($request->brand_ids) && $request->brand_ids != "") {
                $brand_ids = explode(",", $request->brand_ids);
                $products = $products->whereIn('p.brand_id', $brand_ids);
            }
            if (isset($request->sizes) && $request->sizes != "" && isset($request->unit_ids) && $request->unit_ids != "") {
                $sizes = explode(",", $request->sizes);
                $unit_ids = explode(",", $request->unit_ids);
                $products = $products->whereIn('pv.measurement', $sizes)->whereIn('pv.stock_unit_id', $unit_ids);
            }
//dd($products->get()->count());
            // if ($where != "") {
            //     $products = $products->whereRaw(substr($where, 4));
            // }
            // dd($products->get()->count());
            $products_total = $products->get()->count();
           
            $products = $products->orderByRaw($sort)->skip($offset)->take($limit)->get();


            $products = $products->makeHidden(['seller_id','row_order','return_status',
                'cancelable_status','till_status','description','status','is_approved','return_days','pincodes',
                'cod_allowed','pickup_location','tags','d_type','seller_name','seller_slug','seller_status',
                'created_at', 'updated_at','deleted_at','image','other_images']);

            $i = 0;

            foreach ($products as $row){

                $sql = ProductVariant::select('*',
                    DB::raw("(SELECT short_code FROM units u WHERE u.id=pv.stock_unit_id) as stock_unit_name"))
                    ->from('product_variants as pv')
                    ->where('pv.product_id','=',$row['id'])
                    ->orderBy('pv.status','ASC');
                $variants = $sql->get();
                $variants = $variants->makeHidden(['product_id','status','measurement_unit_id','stock_unit_id','deleted_at']);
                if (empty($variants)) {
                    continue;
                }

              
                CommonHelper::getProductDetails($row['id'],$user_id,false);
                $variantArray = array();
                for ($k = 0; $k < count($variants); $k++) {
                    array_push($variantArray,CommonHelper::getProductVariant($variants[$k]['id'],$user_id));
                }
                $products[$i]['variants'] = $variantArray;
                $i++;
            }
//$total = DB::table('products')->where('status',1)-> where('is_approved', 1)->whereIn('seller_id', $seller_ids)->count();
$productSql = Product::from('products as p')->select(
                DB::raw('COUNT(p.id) AS total'),
                DB::raw('MIN((select MIN(if(discounted_price > 0, discounted_price, price)) from product_variants where product_variants.product_id = p.id)) as min_price'),
                DB::raw('MAX((select MAX(if(discounted_price > 0, discounted_price, price)) from product_variants where product_variants.product_id = p.id)) as max_price')
            )->leftJoin('product_variants as pv', 'pv.product_id', '=', 'p.id')->where('p.seller_id', $seller_id);
            
            
            if (isset($request->min_price) && isset($request->max_price) && intval($request->max_price)) {
                $productSql = $productSql->havingRaw(" min_price > " . intval(intval($request->min_price) - 1) . " and max_price < " . intval(intval($request->max_price) + 1));
            }

            if (isset($request->brand_ids) && $request->brand_ids != "") {
                $brand_ids = explode(",", $request->brand_ids);
                $productSql = $productSql->whereIn('p.brand_id', $brand_ids);
            }
            if (isset($request->sizes) && $request->sizes != "" && isset($request->unit_ids) && $request->unit_ids != "") {
                $sizes = explode(",", $request->sizes);
                $unit_ids = explode(",", $request->unit_ids);
                $productSql = $productSql->whereIn('pv.measurement', $sizes)->whereIn('pv.stock_unit_id', $unit_ids);
            }

            if ($where != "") {
                $productSql = $productSql->whereRaw(substr($where, 4));
            }
            

            $productResult = $productSql->first();
            $total_min_price = $productResult->min_price;
            $total_max_price = $productResult->max_price;
            $total =  $productResult->total;

            if (!empty($products)) {
                $brands = CommonHelper::getBrandsHavingProducts();
                $sizes = CommonHelper::getProductVariantsSize();
                //return CommonHelper::responseWithData($products,$total);
                return Response::json(array(
                    'status' => 1,
                    'message' => 'success',
                    'total' => $products_total,

                    // 'min_price' => $min_price ?? 0,
                    // 'max_price' => $max_price ?? 0,
                    // 'total_min_price' => $total_min_price ?? 0,
                    // 'total_max_price' => $total_max_price ?? 0,

                    // 'brands' => $brands,
                    // 'sizes' => $sizes,
                    'data' => $products
                ));
            } else {
                return CommonHelper::responseError(__('no_products_found'));
            }
            //dd($products);

        } catch (\Exception $e) {
            Log::info("Products Error : " . $e->getMessage());
            throw $e;
            return CommonHelper::responseError("Something Went Wrong!");
        }



    }

    public function getActiveProducts(){
        $products = Product::where('status', 1)->orderBy('id','DESC')->get()->toArray();
        return CommonHelper::responseWithData($products);
    }

    public function save(Request $request){
        // \Log::info('Save : ',[$request->all()]);
        $validator = Validator::make($request->all(),[
            'name' => [ 'required',
                        Rule::unique('products')->where(function($query) use ($request) {
                            $query->where('seller_id', $request->seller_id);
                        })
                ],
          //  'slug' => 'required|unique:products,slug',
            'seller_id' => 'required',
            'description' => 'required',
            'image' => 'required|mimes:jpeg,jpg,png',
            'type' => 'required',
            'is_unlimited_stock' => 'required',

            'packet_measurement.*' =>  ['required_if:type,packet','numeric', Rule::notIn([0]),],
            'packet_price.*' =>  ['required_if:type,packet','numeric'],
            'packet_stock.*' =>  ['required_if:type,packet','numeric',
                function ($attribute, $value, $fail) use ($request) {
                    if ($request->input('is_unlimited_stock') == 0 && $value == 0) {
                        $fail($attribute.' must be greater than 0 when is_unlimited_stock is 0.');
                    }
                },],
            'packet_stock_unit_id.*' =>  ['required_if:type,packet','numeric'],

            'loose_measurement.*' =>  ['required_if:type,loose','numeric', Rule::notIn([0]),],
            'loose_price.*' =>  ['required_if:type,loose','numeric'],
            'category_id' => 'required',
        ],[
            'name.unique' => 'The product name has already been taken.',
            'seller_id.required' => 'The seller name field is required.',
            'is_unlimited_stock.required' => 'The Stock Limit field is required.',
            'category_id.required' => 'The Category name field is required.',
            'packet_measurement.*.required_if' => 'The Packet Measurement is required when the type is "Packet".',
            'packet_measurement.*.numeric' => 'The Packet Measurement  must be a number.',
            'packet_measurement.*.not_in' => 'The Packet Measurement must not be zero.',
            'packet_stock.*.required_if' => 'The Packet Stock is required when the type is "Packet".',
            'packet_stock.*.not_in' => 'The Packet Stock must not be zero.',
            'packet_stock_unit_id.*.required_if' => 'The Packet Stock Unit is required when the type is "Packet".',

            'loose_measurement.*.required_if' => 'The Loose Measurement is required when the type is "Loose".',
            'loose_measurement.*.numeric' => 'The Loose Measurement  must be a number.',
            'loose_measurement.*.not_in' => 'The Loose Measurement must not be zero.',

        ]);
        if ($validator->fails()) {
            return CommonHelper::responseError($validator->errors()->first());
        }

        $variations = array();
        if($request->type == "packet") {
            foreach ($request->packet_measurement as $index => $item) {
                $data = array();
                $data['measurement'] = $request->packet_measurement[$index];
                $data['price'] = $request->packet_price[$index];
                $data['discounted_price'] = $request->discounted_price[$index];
                $data['status'] = $request->packet_status[$index];
                $data['stock'] = $request->packet_stock[$index];
                $data['stock_unit_id'] = $request->packet_stock_unit_id[$index];
                $variations[] = $data;
            }
        }else{
            foreach ($request->loose_measurement as $index => $item) {
                $data = array();
                $data['measurement'] = $request->loose_measurement[$index];
                $data['price'] = $request->loose_price[$index];
                $data['discounted_price'] = $request->loose_discounted_price[$index];
                $variations[] = $data;
            }
        }
        if (count($variations) !== count(array_unique($variations, SORT_REGULAR))) {
            return CommonHelper::responseError("Variations are duplicate!");
        }

        if($request->max_allowed_quantity == "" || $request->max_allowed_quantity == 0 ){
            $max_allowed_quantity = Setting::get_value('max_cart_items_count');
            if($max_allowed_quantity == "" || $max_allowed_quantity == 0 ){
                return CommonHelper::responseError("Maximum items allowed in cart in empty in store settings.");
            }
        }else{
            $max_allowed_quantity = $request->max_allowed_quantity;
        }

        DB::beginTransaction();
        try {
            $row_order = Product::max('row_order') + 1;
            $product = new Product();
            $product->name = $request->name;
            $product->slug = Str::slug($request->name);
            $product->row_order = $row_order;
            $product->tax_id = $request->tax_id ?? "";
            $product->brand_id = $request->brand_id ?? "";
            $product->seller_id = $request->seller_id;
            $product->tags = $request->tags ?? "";
            $product->type = $request->type;
            $product->category_id = $request->category_id;
            $product->indicator = $request->product_type;
            $product->manufacturer = $request->manufacturer;
            $product->made_in = $request->made_in;
            $product->tax_included_in_price = $request->tax_included_in_price;
            $product->return_status = $request->return_status;
            $product->return_days = $request->return_days;
            $product->cancelable_status = $request->cancelable_status;
            $product->till_status = $request->till_status;
            $product->cod_allowed = $request->cod_allowed_status;
            $product->total_allowed_quantity = $max_allowed_quantity;
            $product->description = $request->description;
            $product->is_unlimited_stock = $request->is_unlimited_stock;
            $product->is_approved = $request->is_approved;
            $product->status = 1;
            $product->brand_id = $request->brand_id;
            $product->fssai_lic_no = $request->fssai_lic_no ?? "";
            if($request->fssai_lic_no != null){
                $pattern = '/^[0-9]{14}$/';
                // Check if the FSSAI number matches the pattern
                if (preg_match($pattern, $request->fssai_lic_no)) {
                
                } else {
                    return CommonHelper::responseError("Please enter valid FSSAI no.");
                }
            }
            $image = '';
            if($request->hasFile('image')){
                $file = $request->file('image');
                $fileName = time().'_'.rand(1111,99999).'.'.$file->getClientOriginalExtension();
                $image = Storage::disk('public')->putFileAs('products', $file, $fileName);
            }
            $product->image = $image;
            $product->save();

            if($request->hasFile('other_images')){
                CommonHelper::uploadProductImages($request->file('other_images'),$product->id);
            }

            /*Variance*/
            if($request->type == "packet"){

                foreach ($request->packet_measurement as $index => $item){

                    $data = array();
                    $data['product_id'] = $product->id;
                    $data['type'] = $request->type;
                    $data['measurement'] = $request->packet_measurement[$index];
                    $data['price'] = $request->packet_price[$index];
                    $data['discounted_price'] = isset($request->discounted_price[$index]) ? $request->discounted_price[$index] : 0;
                    $data['status'] = $request->packet_status[$index] ?? 1;
                    $data['stock'] = $request->packet_stock[$index];
                    $data['stock_unit_id'] = isset($request->packet_stock_unit_id[$index]) ? $request->packet_stock_unit_id[$index] : 0;

                    ProductVariant::insert($data);
                    $variant_id = DB::getPdo()->lastInsertId();
                    if($request->hasFile('packet_variant_images_'.$index)){
                        CommonHelper::uploadProductImages($request->file('packet_variant_images_'.$index),$product->id, $variant_id);
                    }
                }
            }

            if($request->type == "loose"){
                foreach ($request->loose_measurement as $index => $item){

                    $data = array();
                    $data['product_id'] = $product->id;
                    $data['type'] = $request->type;
                    $data['stock'] = $request->loose_stock;
                    $data['stock_unit_id'] = $request->loose_stock_unit_id;
                    $data['status'] = $request->status;
                    $data['measurement'] = $request->loose_measurement[$index];
                    $data['price'] = $request->loose_price[$index];

                    $data['discounted_price'] = isset($request->loose_discounted_price[$index]) ? $request->loose_discounted_price[$index] : 0;

                    ProductVariant::insert($data);
                    $variant_id = DB::getPdo()->lastInsertId();
                    if($request->hasFile('loose_variant_images_'.$index)){
                        CommonHelper::uploadProductImages($request->file('loose_variant_images_'.$index),$product->id, $variant_id);
                    }
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            Log::info("Error : ".$e->getMessage());
            DB::rollBack();
            // throw $e;
            return CommonHelper::responseError($e->getMessage());
        }

        return CommonHelper::responseSuccess("Product Saved Successfully!");
    }

    public function edit($id){
        $product = Product::with('seller','images','variants.images', 'variants.unit','category', 'tax','madeInCountry')
            ->where('id',$id)->first();
        //log::info('product edit function :=> ',[$product]);
        if(!$product){
            return CommonHelper::responseError("Product Not found!");
        }
        return CommonHelper::responseWithData($product);
    }

    public function update(Request $request){
       
        $validator = Validator::make($request->all(),[
            'name' =>[ 'required',
                Rule::unique('products')->where(function($query) use ($request) {
                    $query->where('seller_id', $request->seller_id)->where('id', '!=', $request->id);
                })
            ],
            //'slug' => 'required|unique:products,slug,'.$request->id,
            'seller_id' => 'required',
            'description' => 'required',

            'type' => 'required',
            'is_unlimited_stock' => 'required',

            'packet_measurement.*' =>  ['required_if:type,packet','numeric', Rule::notIn([0]),],
            'packet_price.*' =>  ['required_if:type,packet','numeric'],
            'packet_stock.*' =>  ['required_if:type,packet','numeric',
                function ($attribute, $value, $fail) use ($request) {
                    if ($request->input('is_unlimited_stock') == 0 && $value == 0) {
                        $fail('The Packet Stock must be greater than 0 when Limited Stock Limit');
                    }
                },],
            'packet_stock_unit_id.*' =>  ['required_if:type,packet','numeric'],

            'loose_measurement.*' =>  ['required_if:type,loose','numeric', Rule::notIn([0]),],
            'loose_price.*' =>  ['required_if:type,loose','numeric'],

            'category_id' => 'required',
        
        ],[
            'name.unique' => 'The product name has already been taken.',
            'seller_id.required' => 'The seller name field is required.',
            'is_unlimited_stock.required' => 'The Stock Limit field is required.',
            'category_id.required' => 'The Category name field is required.',
            'packet_measurement.*.required_if' => 'The Packet Measurement is required when the type is "Packet".',
            'packet_measurement.*.numeric' => 'The Packet Measurement  must be a number.',
            'packet_measurement.*.not_in' => 'The Packet Measurement must not be zero.',
            'packet_stock.*.required_if' => 'The Packet Stock is required when the type is "Packet".',
            'packet_stock.*.not_in' => 'The Packet Stock must not be zero.',
            'packet_stock_unit_id.*.required_if' => 'The Packet Stock Unit is required when the type is "Packet".',

            'loose_measurement.*.required_if' => 'The Loose Measurement is required when the type is "Loose".',
            'loose_measurement.*.numeric' => 'The Loose Measurement  must be a number.',
            'loose_measurement.*.not_in' => 'The Loose Measurement must not be zero.',
        ]);
        if ($validator->fails()) {
            return CommonHelper::responseError($validator->errors()->first());
        }
        $variations = array();
        if($request->type == "packet") {
            foreach ($request->packet_measurement as $index => $item) {
                $data = array();
                $data['measurement'] = $request->packet_measurement[$index];
                $data['price'] = $request->packet_price[$index];
                $data['discounted_price'] = $request->discounted_price[$index];
                $data['status'] = $request->packet_status[$index];
                $data['stock'] = $request->packet_stock[$index];
                $data['stock_unit_id'] = $request->packet_stock_unit_id[$index];
                $variations[] = $data;
            }
        }else{
            foreach ($request->loose_measurement as $index => $item) {
                $data = array();
                $data['measurement'] = $request->loose_measurement[$index];
                $data['price'] = $request->loose_price[$index];
                $data['discounted_price'] = $request->loose_discounted_price[$index];
                $variations[] = $data;
            }
        }
        if (count($variations) !== count(array_unique($variations, SORT_REGULAR))) {
            return CommonHelper::responseError("Variations are duplicate!");
        }


        //Check validation for Same Value Variants
        /*$errors = array();
        $variants = array();
        if($request->type == "packet"){
            foreach ($request->packet_measurement as $index => $item){
                $unit = Unit::find($request->packet_stock_unit_id[$index]);
                $variant_value = $request->packet_measurement[$index].' '.is_null($unit) ? "" : $unit->short_code;
                if(in_array($variant_value,$variants)){
                    array_push($errors,$variant_value);
                }else{
                    array_push($variants,$variant_value);
                }
            }

        }elseif($request->type == "loose") {

            foreach ($request->loose_measurement as $index => $item) {
                $unit = Unit::find($request->loose_stock_unit_id);
                $variant_value = $request->loose_measurement[$index].' '.is_null($unit) ? "" : $unit->short_code;

                if(in_array($variant_value,$variants)){
                    array_push($errors,$variant_value);
                }else{
                    array_push($variants,$variant_value);
                }
            }
        }

        if(count($errors)>0){
            $errors = array_unique($errors);
            $errors = "Variant already exist with value : ".implode(", ",$errors);
            return CommonHelper::responseError($errors);
        }*/


        if($request->max_allowed_quantity == "" || $request->max_allowed_quantity == 0 ){
            $max_allowed_quantity = Setting::get_value('max_cart_items_count');
            if($max_allowed_quantity == "" || $max_allowed_quantity == 0 ){
                return CommonHelper::responseError("Maximum items allowed in cart in empty in store settings.");
            }
        }else{
            $max_allowed_quantity = $request->max_allowed_quantity;
        }

        DB::beginTransaction();
        try {
            $product_image_ids = json_decode($request->deleteImageIds);
            if(count($product_image_ids) !==0){
                foreach ($product_image_ids as $index => $product_image_id){
                    $image = ProductImages::find($product_image_id);
                    if($image){
                        //@Storage::disk('public')->delete($image->image);
                        $image->delete();
                    }
                }
            }

            $product = Product::find($request->id);
            $row_order = Product::max('row_order') + 1;
            $product->name = $request->name;

            $product->slug = Str::slug($request->name);

            $product->row_order = $row_order;
            $product->tax_id = $request->tax_id;
            $product->brand_id = $request->brand_id;
            $product->seller_id = $request->seller_id;
            $product->tags = $request->tags ?? "";
            $product->type = $request->type;
            $product->category_id = $request->category_id;
            $product->indicator = $request->product_type;
            $product->manufacturer = $request->manufacturer;
            $product->made_in = $request->made_in;
            $product->tax_included_in_price = $request->tax_included_in_price;
            $product->return_status = $request->return_status;
            $product->return_days = $request->return_days;
            $product->cancelable_status = $request->cancelable_status;
            $product->till_status = $request->till_status;
            $product->cod_allowed = $request->cod_allowed_status;
            $product->total_allowed_quantity = $max_allowed_quantity;
            $product->description = $request->description;
            $product->is_unlimited_stock = $request->is_unlimited_stock;
            $product->is_approved = $request->is_approved;
            $product->fssai_lic_no = $request->fssai_lic_no ?? "";
            if($request->fssai_lic_no != null){
                $pattern = '/^[0-9]{14}$/';
                // Check if the FSSAI number matches the pattern
                if (preg_match($pattern, $request->fssai_lic_no)) {
                
                } else {
                    return CommonHelper::responseError("Please enter valid FSSAI no.");
                }
            }

            if($request->hasFile('image')){
                $file = $request->file('image');
                $fileName = time().'_'.rand(1111,99999).'.'.$file->getClientOriginalExtension();
                $image = Storage::disk('public')->putFileAs('products', $file, $fileName);
                $product->image = $image;
            }
            if($request->hasFile('other_images')){
                CommonHelper::uploadProductImages($request->file('other_images'),$request->id);
            }
            $product->save();

            //Variance
            if($request->type == "packet"){
                foreach ($request->packet_measurement as $index => $item){
                    $variant = ProductVariant::find($request->variant_id[$index]);
                    if(!$variant){
                        $variant = new ProductVariant();
                    }
                    $variant->product_id = $product->id;
                    $variant->type = $request->type;
                    $variant->measurement = $request->packet_measurement[$index];
                    $variant->price = $request->packet_price[$index];
                    $variant->discounted_price = isset($request->discounted_price[$index]) ? $request->discounted_price[$index] : 0;
                    $variant->status = $request->packet_status[$index];
                    $variant->stock = ($request->is_unlimited_stock == 0)?$request->packet_stock[$index]:0;
                    $variant->stock_unit_id = isset($request->packet_stock_unit_id[$index]) ? $request->packet_stock_unit_id[$index] : 0;
                    $variant->save();
                    if($request->hasFile('packet_variant_images_'.$index)){
                        CommonHelper::uploadProductImages($request->file('packet_variant_images_'.$index),$product->id, $variant->id);
                    }
                }
            }

            if($request->type == "loose"){
                foreach ($request->loose_measurement as $index => $item){
                    $variant = ProductVariant::find($request->variant_id[$index]);
                    if(!$variant){
                        $variant = new ProductVariant();
                    }
                    $variant->product_id = $product->id;
                    $variant->type = $request->type;
                    $variant->stock = ($request->is_unlimited_stock == 0) ? $request->loose_stock:0;
                    $variant->stock_unit_id = $request->loose_stock_unit_id;
                    $variant->status = $request->status;
                    $variant->measurement = $request->loose_measurement[$index];
                    $variant->price = $request->loose_price[$index];
                    $variant->discounted_price = isset($request->loose_discounted_price[$index]) ? $request->loose_discounted_price[$index] : 0;
                    $variant->save();
                    if($request->hasFile('loose_variant_images_'.$index)){
                        CommonHelper::uploadProductImages($request->file('loose_variant_images_'.$index),$product->id, $variant->id);
                    }
                }
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::info("Error : ".$e->getMessage());
            throw $e;
            return CommonHelper::responseError("Something Went Wrong!");
        }
        return CommonHelper::responseSuccess("Product Updated Successfully!");
    }

    public function delete(Request $request){
        if(isset($request->id)){

            $productVariant= ProductVariant::find($request->id);
            
            if($productVariant){
                $product_id = $productVariant->product_id;


                //@Storage::disk('public')->delete($category->image);
                $variantDeleteStatus =  $productVariant->delete();


                $variants = ProductVariant::where('product_id',$product_id)->get();
                if($variantDeleteStatus == true && $variants->count() == 0){
                    $product = Product::find($product_id);
                    if($product){
                        $product->delete();
                    }
                }

                return CommonHelper::responseSuccess("Product Deleted Successfully!");
            }else{
                return CommonHelper::responseSuccess("Product Already Deleted!");
            }
        }
    }

    public function multipleDelete(Request $request){
        if(isset($request->ids)){
            $ids = explode(',' , $request->ids);
            $productVariants = ProductVariant::with('images')->whereIn('id',$ids)->get();
            foreach($productVariants as $productVariant) {
                $product_id = $productVariant->product_id;
                foreach ($productVariant->images as $image){
                    @Storage::disk('public')->delete($image->image);
                    $image->delete();
                }
                $productVariant->delete();

                //If All variant deleted remove main product
                $product = Product::with('variants','images')->where('id',$product_id)->first();
                if($product && count($product->variants)==0){
                    foreach ($productVariant->images as $image){
                        @Storage::disk('public')->delete($image->image);
                        $image->delete();
                    }
                    @Storage::disk('public')->delete($product->image);
                    $product->delete();
                }
            }
            return CommonHelper::responseSuccess("Selected all Product Deleted Successfully!");
        }
    }

    public function changeStatus(Request $request){
        if(isset($request->id)){
            $product = Product::find($request->id);
            if($product){
                //dd($product->status);
                $product->status = ($product->status == 1)?0:1;
                $product->save();
                return CommonHelper::responseSuccess("Products Status Updated Successfully!");
            }else{
                return CommonHelper::responseSuccess("Products Record not found!");
            }

        }
    }

    public function getProductsOrderList(Request $request){

        $validator = Validator::make($request->all(), [
            'category_id' => 'required',
        ]);
        if ($validator->fails()) {
            return CommonHelper::responseError($validator->errors()->first());
        }

        $products = Product::where('category_id',$request->category_id)->orderBy('row_order','ASC')->get();
        return CommonHelper::responseWithData($products);
    }
    public function updateProductsOrder(Request $request){
        $products = $request->all();
        foreach ($products as $key => $product){
            $data = Product::find($product["id"]);
            $data->row_order = $product["row_order"];
            $data->save();
        }
        return CommonHelper::responseSuccess("Product Order Updated Successfully!");
    }

    public function bulkUpload(Request $request){
        $validator = Validator::make($request->all(),[
            'file' => 'required'
        ]);
        if ($validator->fails()) {
            return CommonHelper::responseError($validator->errors()->first());
        }

        $filename = $_FILES["file"]["tmp_name"];
        $allowed_status = array("received", "processed", "shipped");
        if ($_FILES["file"]["size"] > 0 ) {
            $file = fopen($filename, "r");
            $count = 0;
            while (($products = fgetcsv($file, 10000, ",")) !== FALSE) {
                if($count!=0) {
                    if (empty($products[0])) {
                        return CommonHelper::responseError('Product Name  is empty at row - ' . $count);
                    }
                    if (empty($products[1])) {
                        return CommonHelper::responseError('Category ID  is empty or invalid at row - ' . $count);
                    }
                    if (empty($products[11])) {
                        return CommonHelper::responseError('Seller ID  is empty or invalid at row - ' . $count);
                    }
                    if (!empty($products[1])) {
                        $seller = Seller::select('name','categories')->where('id',$products[11])->first();
                        if (empty($seller)) {
                            return CommonHelper::responseError('Seller is not exist check the seller_id at row - ' . $count);
                        }
                        if (strpos($seller->categories, $products[1]) === false) {
                            return CommonHelper::responseError('Category ID  is not assign to seller at row - ' . $count);
                        }
                    }
                    if (!empty($products[6]) && $products[6] != 1) {
                        return CommonHelper::responseError('Is Returnable is invalid at row - ' . $count);
                    }
                    if (!empty($products[7]) && $products[7] != 1) {
                        return CommonHelper::responseError('Is cancel-able is invalid at row - ' . $count);
                    }
                    if (!empty($products[7]) && $products[7] == 1 && (empty($products[8]) || !in_array($products[8], $allowed_status))) {
                        return CommonHelper::responseError('Till status is empty or invalid at row - ' . $count);
                    }
                    if (empty($products[7]) && !(empty($emapData[8]))) {
                        return CommonHelper::responseError('Till status is invalid at row - ' . $count);
                    }
                    if (empty($products[9])) {
                        return CommonHelper::responseError('Description  is empty at row - ' . $count);
                    }
                    if (empty($products[10])) {
                        return CommonHelper::responseError('Image  is empty at row - ' . $count);
                    }
                    if (!empty($products[16])) {
                        $tax = Tax::where('id',$products[11])->first();
                        if (empty($tax)) {
                            return CommonHelper::responseError('Tax ID  is invalid at row - ' . $count);
                        }
                    }

                    $index1 = 17;
                    $total_variants = 0;
                    for ($j = 0; $j < 50; $j++) {
                        if (!empty($emapData[$index1])) {
                            $total_variants++;
                        }
                        $index1 = $index1 + 8;
                    }
                    if ($total_variants == 0) {
                        return CommonHelper::responseError('Atleast one variant required at row - ' . $count);
                    }

                    $ids = Unit::select('id')->orderBy('id','ASC')->get();
                    $index1 = 17;
                    for ($z = 0; $z < $total_variants; $z++) {
                        if (empty($products[$index1]) || ($products[$index1] != 'packet' && $products[$index1] != 'loose')) {
                            return CommonHelper::responseError('Type  is empty or invalid at row - ' . $count . ' Index - ' . $index1);
                        }
                        $index1 = $index1 + 1;
                        if (empty($products[$index1]) || !is_numeric($products[$index1])) {
                            return CommonHelper::responseError('Measurement  is empty or invalid at row - ' . $count . ' Index - ' . $index1);
                        }
                        $index1 = $index1 + 1;
                        $invalid_measurement_unit = 1;
                        foreach ($ids as $id) {
                            if ($products[$index1] == $id->id) {
                                $invalid_measurement_unit = 0;
                            }
                        }
                        if (empty($products[$index1]) || $invalid_measurement_unit == 1) {
                            return CommonHelper::responseError('Measurement Unit ID is empty or invalid at row - ' . $count . ' Index - ' . $index1);
                        }
                        $index1 = $index1 + 1;
                        if (empty($products[$index1]) || $products[$index1] <= $products[$index1 + 1]) {
                            return CommonHelper::responseError('Price is empty or invalid at row - ' . $count . ' Index - ' . $index1);
                        }
                        $index1 = $index1 + 2;
                        if (empty($products[$index1]) || ($products[$index1] != 'Available' && $products[$index1] != 'Sold Out')) {
                            return CommonHelper::responseError('Serve For  is empty or invalid at row - ' . $count . ' Index - ' . $index1);
                        }
                        $index1 = $index1 + 1;
                        if ($products[$index1] == '' || !is_numeric($products[$index1])) {
                            return CommonHelper::responseError('Stock  is empty or invalid at row - ' . $count . ' Index - ' . $index1);
                        }
                        $index1 = $index1 + 1;
                        $invalid_stock_unit = 1;
                        foreach ($ids as $id) {
                            if ($products[$index1] == $id['id']) {
                                $invalid_stock_unit = 0;
                            }
                        }
                        if (empty($products[$index1]) || $invalid_stock_unit == 1) {
                            return CommonHelper::responseError('Stock Unit ID is empty or invalid at row - ' . $count . ' Index - ' . $index1);
                        }
                        $index1 = $index1 + 1;
                    }
                }

            }
            fclose($file);

            DB::beginTransaction();
            try {

                $file = fopen($filename, "r");
                $count1 = 0;
                while (($emapData = fgetcsv($file, 10000, ",")) !== FALSE) {

                    if ($count1 != 0) {

                        $seller = Seller::select('name','categories')->where('id',$emapData[11])->first();
                        $is_approved = isset($seller->require_products_approval) && $seller->require_products_approval == 0 ? 1 : 0;

                        $product_name = $emapData[0]; // product name
                        $category_id = $emapData[1]; // category id
                        //$subcategory_id = !empty($emapData[2]) ? $emapData[2] : 0; // subcategory id
                        $indicator = $emapData[3]; // indicator
                        $manufacturer = $emapData[4]; // manufacturer
                        $made_in = $emapData[5]; // made in
                        $return_status = !empty($emapData[6]) ? $emapData[6] : 0; // return status
                        $cancel_status = !empty($emapData[7]) ? $emapData[7] : 0; // cancel status
                        $till_status = $emapData[8]; // till status
                        $description = $emapData[9]; // description
                        $image = str_replace(' ', '-', strtolower($emapData[10])); // image
                        $seller_id = $emapData[11]; // seller_id
                        $is_approved = (!empty($emapData[12]) && $emapData[12] != "") ? $emapData[12] : 0; // is_approved
                        //$deliverable_type = (!empty($emapData[13]) && $emapData[13] != "") ? $emapData[13] : ""; // deliverable_type
                        $brand_id =  (!empty($emapData[14]) && $emapData[14] != "") ? $emapData[14] : 0; // brand_id
                        $return_days =  (!empty($emapData[15]) && $emapData[15] != "") ? $emapData[15] : "0"; // return_days
                        $tax_id =  (!empty($emapData[16]) && $emapData[16] != "") ? $emapData[16] : "0"; // tax_id
                        $fssai_lic_no =  (!empty($emapData[17]) && $emapData[17] != "") ? $emapData[17] : ""; // fssai_lic_no
                        $type =  (!empty($emapData[18]) && $emapData[18] != "") ? $emapData[18] : ""; // type
                        

                        /*$emapData[17] = trim($db->escapeString($emapData[17])); // type
                        $emapData[18] = trim($db->escapeString($emapData[18])); // Measurement
                        $emapData[19] = trim($db->escapeString($emapData[19])); // Measurement Unit ID
                        $emapData[20] = trim($db->escapeString($emapData[20])); // Price
                        $emapData[21] = trim($db->escapeString($emapData[21])); // Discounted Price
                        $emapData[22] = trim($db->escapeString($emapData[22])); // Serve For
                        $emapData[23] = trim($db->escapeString($emapData[23])); // Stock
                        $emapData[24] = trim($db->escapeString($emapData[24])); // Stock Unit ID
                        $emapData[24] = trim($db->escapeString($emapData[25])); // weight
                        $emapData[24] = trim($db->escapeString($emapData[26])); // height
                        $emapData[24] = trim($db->escapeString($emapData[27])); // breadth
                        $emapData[24] = trim($db->escapeString($emapData[28])); // length*/
                        //$slug = $function->slugify($emapData[0]);

                        $row_order = Product::max('row_order') + 1;
                        $product = new Product();
                        $product->name = $product_name; // product name

                        $product->row_order = $row_order; // row_order
                        $product->slug = CommonHelper::slugify($emapData[0]);
                        $product->tags = $emapData[0];
                        $product->status = 1;
                        $product->cod_allowed = 1;
                        $product->total_allowed_quantity = 3;

                        $product->category_id = $category_id; // category id
                        //$product->subcategory_id = $subcategory_id;
                        $product->indicator = $indicator; // indicator
                        $product->manufacturer = $manufacturer; // manufacturer
                        $product->made_in = $made_in; // made in
                        $product->return_status = $return_status; // return status
                        $product->cancelable_status = $cancel_status; // cancel status
                        $product->till_status = $till_status; // till status
                        $product->description = $description; // description
                        $product->image = $image; // image
                        $product->seller_id = $seller_id; // seller_id
                        $product->is_approved = $is_approved; // is_approved
                        //$product->standard_shipping = $deliverable_type; // deliverable_type
                        $product->brand_id = $brand_id; // pincodes
                        $product->return_days = $return_days; // return_days
                        $product->tax_id = $tax_id; // tax_id
                        $product->fssai_lic_no = $fssai_lic_no; // fssai_lic_no
                        $product->type = $type; // type
                        $product->save();

                        $index1 = 18;
                        $total_variants = 0;
                        for ($j = 0; $j < 50; $j++) {
                            if (!empty($emapData[$index1])) {
                                $total_variants++;
                            }
                            $index1 = $index1 + 8;
                        }

                        $index = 18;
                        for ($i = 0; $i < $total_variants; $i++) {
                            $variant = new ProductVariant();
                            $variant->product_id = $product->id;
                            $variant->type = $emapData[$index];
                            $index++;
                            $variant->measurement = $emapData[$index];
                            $index++;
                            //$variant->measurement_unit_id = $emapData[$index];
                            $index++;
                            $variant->price = $emapData[$index];
                            $index++;
                            $variant->discounted_price = $emapData[$index];
                            $index++;
                            $variant->status = $emapData[$index]; // serve_for
                            $index++;
                            $variant->stock = $emapData[$index];
                            $index++;
                            $variant->stock_unit_id = $emapData[$index];
                            $index++;

                            /*$variant->weight = $emapData[$index];
                            $index++;
                            $variant->height = $emapData[$index];
                            $index++;
                            $variant->breadth = $emapData[$index];
                            $index++;
                            $variant->length = $emapData[$index];
                            $index++;*/

                            $variant->save();
                        }
                    }
                    $count1++;
                }
                fclose($file);

                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                Log::info("Error : ".$e->getMessage());
                //throw $e;
                return CommonHelper::responseError("Please review the CSV file and compare it with the sample file or follow the provided instructions.");
            }
            //Excel::import(new ProductsImport, $request->file('file')->store('temp'));
            return CommonHelper::responseSuccess("All Products Imported Successfully!");
        }
    }
    public function getProduct(Request $request){
        $validator = Validator::make($request->all(),[
            'product_id' => 'required',
        ]);
        if ($validator->fails()) {
            return CommonHelper::responseError($validator->errors()->first());
        }
        $product_id=$request->product_id;
        $product = Product::with('seller','images','variants.images', 'variants.unit','category', 'tax','madeInCountry','brand')->where('id',$product_id)->first();
        if(!$product){
            return CommonHelper::responseError("Product Not found!");
        }
        return CommonHelper::responseWithData($product);
    }
}
