<?php
	namespace Phast;

	/**
	 * Provides an enumeration of predefined values for type of WebPage metadata.
	 * @author Michael Becker
	 */
	abstract class WebPageMetadataType extends Enumeration
	{
		/**
		 * The metadata is of type "name".
		 * @var int 0
		 */
		const Name = 0;
		/**
		 * The metadata is of type "http-equiv".
		 * @var int 1
		 */
		const HTTPEquivalent = 1;
		/**
		 * The metadata is of type "property".
		 * @var int 2
		 */
		const Property = 2;
	}
	
    class WebPageMetadata
    {
        public $Name;
        public $Content;
        public $Type;
        
        public function __construct($name, $content = "", $type = WebPageMetadataType::Name)
        {
            $this->Name = $name;
            $this->Content = $content;
            $this->Type = $type;
        }
    }
?>