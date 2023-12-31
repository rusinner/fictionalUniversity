<?php
/**
 * 
 */
class Loco_config_BundleWriter implements JsonSerializable {
    
    /**
     * @var Loco_package_Bundle
     */
    private $bundle;
    
    
    /**
     * Initialize config from the bundle it will describe
     */    
    public function __construct( Loco_package_Bundle $bundle ){
        $this->bundle = $bundle;
    }


    /**
     * @return string XML source
     */
    public function toXml(){
        $model = new Loco_config_XMLModel;
        $dom = $this->compile($model);
        return $dom->saveXML();
    }


    /**
     * @return array
     */
    public function toArray(){
        $model = new Loco_config_ArrayModel;
        $dom = $this->compile($model);
        return $dom->export();
    }


    /**
     * @return Loco_mvc_PostParams
     */
    public function toForm(){
        $model = new Loco_config_FormModel;
        $this->compile($model);
        return $model->getPost();
    }


    /**
     * Alias of toArray implementing JsonSerializable
     * @return array
     */
    #[ReturnTypeWillChange]
    public function jsonSerialize(){
        return $this->toArray();
    }


    /**
     * Agnostic compilation of any config data type
     * @return LocoConfigDocument
     */
    private function compile( Loco_config_Model $model ){
        
        $bundle = $this->bundle;
        $model->setDirectoryPath( $bundle->getDirectoryPath() );
        $systemTargets = $bundle->getSystemTargets();
        
        $dom = $model->getDom();
        $root = $dom->createElement('bundle') ;
        $dom->appendChild($root);
        $root->setAttribute( 'name', $bundle->getName() );

        /*/ additional headers for information only (not read back in)
        if( $value = $bundle->getHeaderInfo()->getVendorHost() ){
            $root->setAttribute( 'vendor', $value );
        }*/
        
        foreach( $bundle->exportGrouped() as $domainName => $projects ){
            $domainElement = $dom->createElement('domain');
            $root->appendChild( $domainElement );
            $domainElement->setAttribute( 'name', $domainName );
            /* @var $proj Loco_package_Project */
            foreach( $projects as $proj ){
                $projElement = $dom->createElement('project');
                $domainElement->appendChild($projElement);
                // add project name even if it's the same as the bundle name
                // when loading however, missing name will default to bundle name
                $value = $proj->getName() or $value = $bundle->getName();
                $projElement->setAttribute( 'name', $value );
                // add project slug even if it's the same as the domain name
                $value = $proj->getSlug();
                $projElement->setAttribute( 'slug', $value );
                // <source>
                // zero or more source file locations
                $sourcesElement = $dom->createElement('source');
                /* @var Loco_fs_File $file */
                foreach( $proj->getConfiguredSources() as $file ){
                    $sourcesElement->appendChild( $model->createFileElement($file) );
                }
                // zero or more excluded source paths
                $excludeElement = $dom->createElement('exclude');
                foreach( $proj->getConfiguredSourcesExcluded() as $file ){
                    $excludeElement->appendChild( $model->createFileElement($file) );
                }
                if( $excludeElement->hasChildNodes() ){
                    $sourcesElement->appendChild($excludeElement);
                }
                if( $sourcesElement->hasChildNodes() ){
                    $projElement->appendChild( $sourcesElement );
                }
                // <target>
                // add zero or more target locations
                $targetsElement = $dom->createElement('target');
                /* @var $file Loco_fs_Directory */
                foreach( $proj->getConfiguredTargets() as $file ){
                    if( ! in_array( $file->getPath(), $systemTargets, true ) ){
                        $targetsElement->appendChild( $model->createFileElement($file) );
                    }
                }
                // zero or more excluded targets
                $excludeElement = $dom->createElement('exclude');
                foreach( $proj->getConfiguredTargetsExcluded() as $file ){
                    $excludeElement->appendChild( $model->createFileElement($file) );
                }
                if( $excludeElement->hasChildNodes() ){
                    $targetsElement->appendChild($excludeElement);
                }
                if( $targetsElement->hasChildNodes() ){
                    $projElement->appendChild( $targetsElement );
                }
                // <template>
                // add single POT template location
                if( $file = $proj->getPot() ){
                    $templateElement = $dom->createElement('template');
                    $projElement->appendChild($templateElement);
                    $templateElement->appendChild( $model->createFileElement($file) );
                    // template may be protected from end-user tampering
                    if( $proj->isPotLocked() ){
                        $templateElement->setAttribute('locked','true');
                    }
                }
            }
        }

        // Write bundle-level path exclusions
        $excludeElement = $dom->createElement('exclude');
        foreach( $bundle->getExcludedLocations() as $file ){
            $excludeElement->appendChild( $model->createFileElement($file) );
        }
        if( $excludeElement->hasChildNodes() ){
            $root->appendChild( $excludeElement );
        }

        return $dom;
    }


    
    
}