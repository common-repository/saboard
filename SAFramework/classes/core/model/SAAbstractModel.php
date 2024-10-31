<?php
if(!class_exists('SAAbstractModel')){
	abstract class SAAbstractModel {
		public function requestMapping(){
			return SAMapper::requestMapping($this);
		}
	}
}