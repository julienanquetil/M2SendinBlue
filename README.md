Installing the Extension
--------------------------------------------------

Add the composer repository

    composer config repositories.julienanquetil vcs https://github.com/julienanquetil/M2SendinBlue
    composer require julienanquetil/magento2-module-m2sendinblue
    composer update julienanquetil/magento2-module-m2sendinblue
    
This command enables the  module that make up the M2SendinBlue extension

    php bin/magento module:enable JulienAnquetil_M2SendinBlue
    
Once a module is enabled, the rest of Magento can "see" it. The last command tells Magento to actually install the module.
   
    php bin/magento setup:upgrade
