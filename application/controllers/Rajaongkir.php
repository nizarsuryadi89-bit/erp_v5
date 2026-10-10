<?php
    class Rajaongkir extends MY_Controller {
        // http://example.com/rajaongkir/province

        /**
         * @return void
         */
        public function province(){
            $provinces = $this->rajaongkir->province(); // output json
            print_r($provinces);
        }
    
        /**
         * @return void
         */
        public function city(){
            $cities = $this->rajaongkir->city(); // output json
            print_r($cities);
        }
    
        /**
         * @return void
         */
        public function subdistrict(){
            $subdistrict = $this->rajaongkir->subdistrict(151); // output json
            print_r($subdistrict);
        }
    
        /**
         * @return void
         */
        public function cost(){
            $cost = $this->rajaongkir->cost(501, 114, 1000, "jne"); // output json
            print_r($cost);
        }
    
    }