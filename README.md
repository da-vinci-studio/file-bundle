# file-bundle

###Installation
1. Add bundle into composer.json

 composer require **da-vinci-studio/file-bundle**
 
2. Register bundle in AppKernel

         public function registerBundles()
         {
           $bundles = [
              // ...
              new \Dvs\FileBundle\DvsFileBundle()
           ];
         }

###Using FileReceiver
FileReceiver provides a way to save files in a specific localization via FlySystem. 
It will take care of your file's name and path generation needed for correct saving.

1. Create a directory for file saving (e.g. **document** in root_dir)  
2. Register directory in **parameters.yml** 

 document_upload_dir: document
 
3. According to FlySystem [documentation](https://github.com/1up-lab/OneupFlysystemBundle/blob/master/Resources/doc/index.md#step3-configure-your-filesystems)
register file system in **config.yml** in connection with created directory

         dvs_file:
           filesystems:
             pp_standard:
               adapter:
                 local:
                   directory: "%kernel.root_dir%/document"

4. Register FileReceiver as a dependency using id: **dvs.file_receiver** 
