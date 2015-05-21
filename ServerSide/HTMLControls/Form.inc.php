<?php
	namespace Phast\HTMLControls;
	
	use Phast\Enumeration;
	use Phast\HTMLControl;
	use Phast\System;
	
	use Phast\WebControl;
	use Phast\WebControlAttribute;
	
	
	/**
	 * Provides an enumeration of predefined values for method of form submission.
	 * @author Michael Becker
	 */
	abstract class FormMethod extends Enumeration
	{
		/**
		 * No method is specified
		 * @var int 0
		 */
		const None = 0;
		/**
		 * Will use the HTTP GET method
		 * @var int 1
		 */
		const Get = 1;
		/**
		 * Will use the HTTP POST method
		 * @var int 2
		 */
		const Post = 2;
	}
	
	class Form extends HTMLControl
	{
		public function __construct($id = null, $method = FormMethod::None)
		{
			parent::__construct($id);
			
			$this->TagName = "form";
			$this->Method = $method;
		}
		
		/**
		 * The URL to direct the user to upon form submission.
		 * @var string
		 */
		public $Action;
		/**
		 * The method of form submission.
		 * @var FormMethod
		 */
		public $Method;
		/**
		 * The type of encoding used to submit this form. Known values are "application/x-www-form-urlencoded" (the default) and "multipart/form-data" (used in combination with the file input element.
		 * @var string
		 */
		public $EncodingType;
		
		protected function RenderBeginTag()
		{
			if ($this->Action != null)
			{
				$this->Attributes[] = new WebControlAttribute("action", System::ExpandRelativePath($this->Action));
			}
			if ($this->EncodingType != null)
			{
				$this->Attributes[] = new WebControlAttribute("enctype", $this->EncodingType);
			}
			if (is_string($this->Method))
			{
				switch (strtolower($this->Method))
				{
					case "get":
					{
						$this->Method = FormMethod::Get;
						break;
					}
					case "post":
					{
						$this->Method = FormMethod::Post;
						break;
					}
				}
			}
			if ($this->Method != FormMethod::None)
			{
				$methodstr = "";
				switch ($this->Method)
				{
					case FormMethod::Get:
					{
						$methodstr = "GET";
						break;
					}
					case FormMethod::Post:
					{
						$methodstr = "POST";
						break;
					}
				}
				$this->Attributes[] = new WebControlAttribute("method", $methodstr);
			}
			parent::RenderBeginTag();
		}
	}
?>