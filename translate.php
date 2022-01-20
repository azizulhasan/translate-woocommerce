<?php
/**
 * Get all strings from targeted  file.
 * Search the string in source file (from whare translation will come) if get.
 * Then put it in taget's  file
 *
 */

class PT_TRANSLATION
{

    protected $files_extensions = array(
        'po',
    );
    protected $taget_folder;
    protected $source_folder;
    protected $demo_file;
    protected $textdomain;
    /**
     * Get all strings from targeted  file.
     * Search the string in source file (from whare translation will come) if get.
     * Then put it in taget's file.
     *
     */

    public function __construct(){
        $this->taget_folder  = './woo_invoice';
        $this->source_folder  = './woocommerce';
        $this->demo_file  = $this->taget_folder."/".'textdomain-and-language-code.po';
        $this->textdomain = 'webappick-pdf-invoice-for-woocommerce';
    }

    /**
     * traslate
     */
    public function pt_translation_each_file()
    {
        
        /**
         * Get source folder files name as an array. From which files translation will be 
         * copied.
         */        
        $source_files = $this->get_files_from_dir($this->source_folder);
        /**
         * Get target folder files name as an array. In which files translation will be 
         * pasted.
         */
        $target_files = $this->get_files_from_dir($this->taget_folder);
        
        $number = 1;
        /**
         * Loop throgh target files. In inner loop it will check in source folder
         * is that file is exist with save language code and file extension. If Yes,
         *  
         */
        foreach($source_files as $source_file_no => $source_file){
            


                $target_file = $this->create_file($source_file);

                $target_words = $this->file_scanner($target_file, '/msgid/i', false);
                
                $source_words = $this->file_scanner($source_file, '/msgid/i', true);
                $this->pt_translate_all_string($target_words, $source_words, $target_file);
                    
                echo "<br/> File Translated : ".$number++;
            
        }
    
    }

    private function create_file($source_file_path){

        $filename = $this->textdomain."-".$this->get_file_extension($source_file_path);
        $new_file = $this->taget_folder."/".$filename;
        $fp = fopen($new_file, 'w');
        copy($this->demo_file, $new_file);
        fclose($fp);
        chmod($new_file, 0777); 

        return  $new_file;
    }

    /**
     * Get files from dictionary recursivly.
     * @return Array All files name.
     */
    protected function get_files_from_dir($dir_name)
    {
        $results = [];
        $files = scandir($dir_name);
        foreach ($files as $key => $value) {
            $path = realpath($dir_name . DIRECTORY_SEPARATOR . $value);
            if (!is_dir($path)) {
                $path_parts = pathinfo($path);
                if (!empty($path_parts['extension']) && in_array($path_parts['extension'], $this->files_extensions)) {
                    $results[] = $path;
                }
            } else if ("." != $value && ".." != $value) {
                $temp = $this->get_files_from_dir($path);
                $results = array_merge($results, $temp);
            }
        }
        return $results;
    }
    /**
     * Get all string from target file and source file.
     * @filename
     * @pattern
     * @line_break
     */
    protected function file_scanner($file, $pattern, $line_break)
    {
        $strings = [];
        /**
         * $line_break will be 'true' for source file from where translation 
         * will be copied as $key and $value.
         * 
         * $key will be source word and $value will be translation of source.
         */
        if(true == $line_break){
            $file_words = fopen($file,"r");
            
            $source_words = [];
            if ($file_words) {
                
                /**
                 * Put all lines which is in double qoutation mark "" of sourse file in an array.
                 */
                while (($line = fgets($file_words)) !== false) {
                    preg_match('/"(.*?)"/', $line, $matches);
                    if(!empty($matches[1])){
                            array_push($source_words, $matches[1]);
                    }
                }
                
                fclose($file_words);
                
                
                /**
                 * Now loop through on $sour_words array and make every source line which is english
                 * as $key and next line as $value which will be translation.
                 */
                $words_arr = [];
                foreach($source_words as $key => $value){
                    if(preg_match('/[a-zA-Z]/', $value)){
                        $next_index = $key+1; // Translation line
                        if(isset($source_words[$next_index])){
                            $words_arr[$value] = $source_words[$next_index];
                        }
                    }
                }
            }
            
            return $words_arr;
        }else{
            /**
             * $line_break will be false for target file. Becaue we only need words for
             * translation.
             * 
             * Get all lines which is in double qoutation mark "" of target file.
             * And put it in array.
             */
            $match_words = preg_grep($pattern, file($file));
            $target_words = [];
            foreach ($match_words as $word){
                preg_match_all('/"(.*?)"/', $word, $matches);
                array_push($target_words, $matches[1][0]);
            }

            return $target_words;
        } 
    }

    /**
     * @param $file path
     * 
     * @return String file extextion and language code.
     * 
     * @examle for arabic language return "ar.po" file convention should be 
     * "plugin-or-theme-texdomain-language-code.po"
     */

    public function get_file_extension($file){
        $ext = explode('\\', $file);
        $filename = end($ext);
        $lang_name = explode('-', $filename);
        $lang_name = end($lang_name);
        return $lang_name;     
    }

    /**
     * 
     * 
     * @param $target_words these words tranlation will be copied from $source_words array
     * @param $source_words these words tranlation will be pasted to $target_words array
     * @param $target_file where translation will be pasted.
     */
    public function pt_translate_all_string($target_words,$source_words, $target_file){
        
        foreach ($target_words as  $target_word){
            if('' != $target_word){
                foreach ($source_words as $source_key => $value){
                // if target word and source word is matches case-insensitively.
                    if(0 == strcasecmp($target_word, $source_key)){ 
                        // get the line number of the word of target file.
                        $lines       = file($target_file);
                        $line_number = false;
                        while (list($source_key, $line) = each($lines) and !$line_number) {
                            $line_number = (strpos($line, $target_word) !== FALSE) ? $source_key + 1 : $line_number;
                        }
                        // put translation into target file.
                        $lines[$line_number] = 'msgstr'.' '. '"'.$value.'"'."\n";
                        file_put_contents( $target_file , implode( "", $lines));
                        echo $target_word."=====".$line_number."=====".$value."<br/>";
                    }
                }
            }
        }

        return true;
    }   
}

$woo_pll = new PT_TRANSLATION();
$woo_pll->pt_translation_each_file();
