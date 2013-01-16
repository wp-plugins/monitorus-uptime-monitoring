<?php
class WPMUC_LanguageManager
{
    /**
     * Print javascript global array of texts
     */ 
	public static function printJsLanguage()
	{
	    WPMUC_Loader::includeLanguageContainer();
        $wpmucLang = WPMUC_LanguageContainer::getLanguageArray();
        
        if(count($wpmucLang)==0)
        {
            return false;
        }
        
        foreach($wpmucLang AS $key => $value)
        {
            $lang_js[] = "'".$key."' : '".$value."'";
        } 
        $lang_js_html = implode(',',$lang_js);
        
        $js = "<script type=\"text/javascript\">
            var wpmucLang = { 
                $lang_js_html 
            };


        </script>";
        
        echo $js;
	}
}
?>