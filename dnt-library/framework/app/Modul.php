<?php

/**
 *  class       Autoload
 *  author      Tomas Doubek
 *  framework   DntLibrary
 *  package     dnt3
 *  date        2017
 */
class Modul extends Database{
	
	
	public $name;
	public $init;
	public $sitemapUrl;
	
	protected function getSitemap($client){
		$this->sitemapUrl = "";
		$query = "
			SELECT id_entity, name_url, type, name, service FROM `dnt_posts` 
			WHERE `dnt_posts`.`type` = 'sitemap' 
			AND `dnt_posts`.`show` > '0' 
			AND `dnt_posts`.`vendor_id` = '".$client->id."' 
			GROUP BY `dnt_posts`.`name_url`
			";
			
		if ($this->num_rows($query) > 0) {
			$this->sitemapUrl = $this->get_results($query, true);
		}
	}
	
	public function getSitemapModules($type = false){
		
		if ($type == "static_view") {
            $eQ = '';
        } else {
            if ($type) {
                $eQ = $type;
            } else {
                $eQ = false;
            }
        }
		$arr = array();
		if($this->sitemapUrl){

			foreach($this->sitemapUrl as $item){
				//var_dump($item->name_url);
				if($item->service == $eQ){
					$arr[] = $item->name_url;
				}
			}
		}
		
		return $arr;
		/*if ($type == "static_view") {
            $eQ = "AND `dnt_posts`.`service` = ''";
        } else {
            if ($type) {
                $eQ = "AND `dnt_posts`.`service` = '" . $type . "'";
            } else {
                $eQ = false;
            }
        }

        if ($vendorId) {
            $vendorId = $vendorId;
        } else {
            $vendorId = $client->id;
        }
		
		$query = "
			SELECT * FROM `dnt_posts` 
			LEFT JOIN `dnt_translates` 
			ON `dnt_posts`.`id_entity` = `dnt_translates`.`translate_id` 
			WHERE `dnt_posts`.`type` = 'sitemap' 
			AND `dnt_translates`.`type` = 'name_url' 
			AND `dnt_posts`.`show` > '0' 
			". $eQ ."
			AND `dnt_posts`.`vendor_id` = '".$vendorId."' 
			GROUP BY `dnt_posts`.`name_url`";
		if ($this->num_rows($query) > 0) {
			foreach ($this->get_results($query) as $row) {
				$arr[] = $row['name_url'];
				if ($row['translate'] != "") {
					$arr[] = $row['translate'];
				}
			}
		} else {
			$arr = array();
		}
		
		return $arr;
		*/
		
	} 
	
	protected function hasPattern($request, $pattern){
		$request = explode("/", ltrim($request, "/"));
		$pattern = explode("/", ltrim($pattern, "/"));
		$returnString = false;
		
		$i=0;
		$compareString = array();
		if(count($request) == count($pattern)){
			//var_dump(count($request), count($pattern));
			foreach($pattern as $singlPattern){
				
				//strict
				if($singlPattern == $request[$i]){
					$compareString[] .= $singlPattern;	
					$i++;
				}
				
				//digit
				if($singlPattern == "{digit}" && is_numeric($request[$i])){
					$compareString[] .= $request[$i];	
					$i++;
				}
				
				//eny
				if($singlPattern == "{eny}" && !empty($request[$i])){
					$compareString[] .= $request[$i];	
					$i++;
				}
				
				//alphabet
				if($singlPattern == "{alphabet}" && ctype_alpha(str_replace("-", "", $request[$i]))){
					$compareString[] .= $request[$i];	
					$i++;
				}
			}
			
			//var_dump($i == count($request));
			if($i == count($request)){
				$returnString = "/".join("/", $compareString);
				//var_dump($returnString);
			}
		}
		return $returnString;
	}
	
	protected function get($client){
		$module = "";
		$file = "dnt-view/layouts/" . $client->layout . "/conf.php";
        if (file_exists($file)) {
            include_once $file;
            if (function_exists("custom_modules")) {
                $custom_modules = custom_modules($this);
            } else {
                $custom_modules = array();
            }
        } else {
            $custom_modules = array();
        }
		
		foreach (array_keys($custom_modules) as $index) {
			foreach($custom_modules[$index] as $key=>$modulUrl){
				if($this->hasPattern($client->requestNoLang, "/".$modulUrl) == $client->requestNoLang){
					$module = $index;
					break;
				}
				
				if("/".$modulUrl == $client->requestNoParam){
					$module = $index;
					break;
				}
				
				if($modulUrl == $client->route(1)){
					$module = $index;
					break;
				}
				
			}
		}
		//var_dump($module, $client->requestNoLang, $client->route(1));exit;
		if($client->route(1) == ""){
			$default = $client->getSetting("startovaci_modul");//Settings::get("startovaci_modul");
			$moduleUrl = $this->getSitemapModules($default);
			if ($default && isset($moduleUrl[0])) {
				$redirect = $client->wwwPath . $client->lang."/" . $moduleUrl[0];
				Dnt::redirect($redirect);
				exit;
			} else {
				$module = DEAFULT_MODUL;
			}
		}
		
		$this->name = $module;
		$this->init = true;
	}
	
	public function load($client, $module){
		$layout = $client->layout;
        $function = "dnt-view/layouts/" . $layout . "/modules/" . $module . "/functions.php";
        $template = "dnt-view/layouts/" . $layout . "/modules/" . $module . "/webhook.php";
        $webhookModule = "dnt-modules/" . $module . "/webhook.php";
        if (file_exists($function))
            include $function;
        if (file_exists($webhookModule)) {
            include $webhookModule;
        } elseif (file_exists($template)) {
            include $template;
        }
	}
	
	public function init($client){
		if(!$this->init){
			$this->getSitemap($client);
			$this->get($client);
			$this->init = true;
		}
	}
	
}
