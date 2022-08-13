<?php
if(!function_exists('price')){
    function price($num){
        return number_format($num,5,'.');
    }
}
