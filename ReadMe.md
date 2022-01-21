# Get localized translation from WooCommerce

This repo is for getting localize translation from woocommerce .po file for your plugin.

# How it Works ?
Suppose you have a plugin based on woocommerce and you want to translate all your strings
in all language. This repo helps to do that. How? Here are few steps you have to do it.

1. Download the repo.
2. Downloading translations from translate.wordpress.org manually.
    a.   Go to https://translate.wordpress.org/projects/wp-plugins/woocommerce and look for your language in the list.
    b. Click the title to be taken to the section for that language.
    ![woocommrerce-laguage](https://woocommerce.com/wp-content/uploads/2012/01/2016-02-17-at-09.57.png?w=950)
    c. Click the heading under Set/Sub Project to view and download a Stable version.
    ![woocommerce-translated-laguage-version](https://woocommerce.com/wp-content/uploads/2012/01/2016-02-17-at-09.59.png?w=950)
    d. Scroll to the bottom for export options. Export a .po file for use on your site.
    ![export](https://woocommerce.com/wp-content/uploads/2012/01/2016-02-17-at-10.00.png?w=950)
    e. Put this file into woocommerce folder of this repo. Which is downloaded to local
    desktop.

3. Now open `translate.php` file to your editor. Change few varable value.
    `
    $this->taget_folder  = './taget_folder';
    $this->source_folder  = './source_folder';
    $this->textdomain = 'webappick-pdf-invoice-for-woocommerce';
    $this->demo_file  = $this->taget_folder."/".'strings-to-translate.po';
    `
    a. `$this->taget_folder` is where tranlation will be pasted after translation for `$this->source_folder`.
    b. `$this->source_folder` from where tranlation will be copied to `$this->taget_folder`.
    c. `$this->textdomain` textdomain of the plugin.
    d. `$this->demo_file` in this file put all of the strings need to be translated in a format.
        i. format is like this
            ` 
            #: includes/class-woo-invoice-pro-template.php:715
            msgid "Billing" 
            msgstr ""
            `
4. After following these steps. Now you can download as many as file by following step 2 and its 
   substeps.

5. Start your Apache and run the `translate.php` file. That it done. All file you put in `$this->source_folder` are translated with there corresponding file. Suppose if in `$this->source_folder` has a file named `wp-plugins-woocommerce-dev-de.po` it will be translated
into `$this->target_folder` as `textdomain-de.po` textdomian which you put `$this->textdomain`
variable.




https://woocommerce.com/document/woocommerce-localization/
/**
 * Get all strings from targeted  file.
 * Search the string in source file (from whare translation will come) if get.
 * Then put it in taget's  file
 *
 */