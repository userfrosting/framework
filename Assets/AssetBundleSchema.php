<?php

/**
 * Asset bundle schema class.  An asset bundle schema contains information about one or more asset bundles.
 * This includes the raw asset file names, as well as file names for the compiled assets.
 *
 * Assumes a schema format compatible with gulp-bundle-assets.
 * @see https://github.com/dowjones/gulp-bundle-assets.
 * @package userfrosting/assets
 * @author  Alexander Weissman
 * @license MIT
 */
namespace UserFrosting\Assets;

use UserFrosting\Support\Exception\FileNotFoundException;
use UserFrosting\Support\Exception\JsonException;

class AssetBundleSchema
{
    /**
     * The array of AssetBundle items that this schema represents.
     *
     * @var array
     */
    protected $bundles;
    
    /**
     * Gets a bundle from this schema.
     *
     * @param string $bundle The name of the bundle.  E.g. "js/main".
     * @return AssetBundle The requested bundle object.
     */
    public function get($bundle)
    {
        if (isset($this->bundles[$bundle]))
            return $this->bundles[$bundle];
        else
            throw new \OutOfBoundsException("Bundle '$bundle' not found in loaded bundles.");
    }    
    
    /**
     * Load a JSON schema file that describes compiled asset bundles, populating compiled asset info for this schema's bundles.
     *
     * Loads information about compiled asset bundles from a JSON file (e.g. bundle.result.json).
     * The format of this file should match the output of gulp-bundle-assets
     * @see https://github.com/dowjones/gulp-bundle-assets
     * @param string $file Path to the schema file.
     * @todo See how this behaves when called multiple times on different files.  It should merge in multiple bundle schemas.
     * @todo Support linting of JSON documents?
     */
    public function loadCompiledSchemaFile($file)
    {
        $doc = file_get_contents($file);
        if ($doc === false)
            throw new FileNotFoundException("The schema '$file' could not be found.");
        $schema = json_decode($doc, true);
        if ($schema === null) {
            throw new JsonException("The schema '$file' does not contain a valid JSON document.  JSON error: " . json_last_error());
        }
        
        $this->loadBundles($schema, false);
    }    
    
    /**
     * Load a JSON schema file that describes raw asset bundles, populating raw asset info for this schema's bundles.
     *
     * Loads information about raw asset bundles from a JSON file (e.g. bundle.config.json).
     * The format of this file should match the input format of gulp-bundle-assets
     * @see https://github.com/dowjones/gulp-bundle-assets
     * @param string $file Path to the schema file.
     * @todo See how this behaves when called multiple times on different files.  It should merge in multiple bundle schemas.
     * @todo Support linting of JSON documents?     
     */    
    public function loadRawSchemaFile($file)
    {
        $doc = file_get_contents($file);
        if ($doc === false)
            throw new FileNotFoundException("The schema '$file' could not be found.");
        $schema = json_decode($doc, true);
        if ($schema === null) {
            throw new JsonException("The schema '$file' does not contain a valid JSON document.  JSON error: " . json_last_error());
        }
        
        if (!isset($schema['bundle'])) 
            throw new \OutOfBoundsException("The specified JSON document does not contain a 'bundle' key.");
        
        $this->loadBundles($schema['bundle'], true);
    }
    
    /**
     * Load a JSON object (as an associative array) that describes asset bundles, populating info for this schema's bundles.
     *
     * The format of this object should match the formats described for bundles in gulp-bundle-assets
     * @see https://github.com/dowjones/gulp-bundle-assets
     * @param array $schema An associative array (usually converted from a JSON object)
     * @param bool $raw True if this schema represents raw info for bundles, false if it represents compiled info.
     * @todo See how this behaves when called multiple times on different schema.  It should merge in multiple bundle schemas.
     */ 
    protected function loadBundles($schema, $raw = false)
    {
        foreach ($schema as $bundle_name => $bundle_schema){
            if (!isset($this->bundles[$bundle_name]))
                $this->bundles[$bundle_name] = new AssetBundle();
            
            // TODO: can a bundle be defined as a string instead of an object/array?
            
            // Load scripts
            if (isset($bundle_schema['scripts'])){
                if (is_array($bundle_schema['scripts']))
                    foreach ($bundle_schema['scripts'] as $script){
                        $this->addBundleScript($this->bundles[$bundle_name], $script, $raw);
                    }
                else if (is_string($bundle_schema['scripts']))
                    $this->addBundleScript($this->bundles[$bundle_name], $bundle_schema['scripts'], $raw);
            }            
            
            // Load styles
            if (isset($bundle_schema['styles'])){
                if (is_array($bundle_schema['styles']))
                    foreach ($bundle_schema['styles'] as $style){
                        $this->addBundleStyle($this->bundles[$bundle_name], $style, $raw);
                    }
                else if (is_string($bundle_schema['styles']))
                    $this->addBundleStyle($this->bundles[$bundle_name], $bundle_schema['styles'], $raw);
            }           
            
            // TODO: load options
            
        } 
    }
    
    /**
     * Add a Javascript asset element to a bundle.
     *
     * @param AssetBundle $bundle A reference to the target bundle.
     * @param array|string $schema A string or associative array containing this asset's info (usually converted from a JSON object)
     * @param bool $raw True if the schema represents raw info for this asset, false if it represents compiled info.
     */
    protected function addBundleScript(&$bundle, $schema, $raw)
    {
        if (is_array($schema) and isset($schema['src']))
            $js_asset = new JavascriptAsset($schema['src']);
        else if (is_string($schema))
            $js_asset = new JavascriptAsset($schema);
        else
            return;
        if ($raw)
            $bundle->addRawJavascriptAsset($js_asset);
        else
            $bundle->addCompiledJavascriptAsset($js_asset);
    }
    
    /**
     * Add a CSS asset element to a bundle.
     *
     * @param AssetBundle $bundle A reference to the target bundle.
     * @param array|string $schema A string or associative array containing this asset's info (usually converted from a JSON object)
     * @param bool $raw True if the schema represents raw info for this asset, false if it represents compiled info.
     */    
    protected function addBundleStyle(&$bundle, $schema, $raw)
    {
        if (is_array($schema) and isset($schema['src']))
            $asset = new CssAsset($schema['src']);
        else if (is_string($schema))
            $asset = new CssAsset($schema);
        else
            return;
        if ($raw)
            $bundle->addRawCssAsset($asset);
        else
            $bundle->addCompiledCssAsset($asset);
    }
}