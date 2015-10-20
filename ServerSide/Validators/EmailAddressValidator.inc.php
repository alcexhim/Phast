<?php

	namespace Phast\Validators;
	
	use Phast\Validator;

	class EmailAddressValidator extends Validator
	{
		public $DomainBlockedMessage;
		public $EmailAddressInvalidMessage;
		
		public $CheckDNSRecords;
		public $PreventThrowawayAddresses;
		public $ThrowawayAddresses;
		
		public static $DefaultDomainBlockedMessage;
		public static $DefaultEmailAddressInvalidMessage;
		
		public static $DefaultThrowawayAddresses;
		
		public function __construct()
		{
			$this->CheckDNSRecords = false;
			$this->PreventThrowawayAddresses = false;
			
			$this->DomainBlockedMessage = EmailAddressValidator::$DefaultDomainBlockedMessage;
			$this->EmailAddressInvalidMessage = EmailAddressValidator::$DefaultEmailAddressInvalidMessage;
			
			$this->ThrowawayAddresses = array();
			foreach (EmailAddressValidator::$DefaultThrowawayAddresses as $addr)
			{
				$this->ThrowawayAddresses[] = $addr;
			}
		}
		
		protected function ValidateInternal($value)
		{
			$domainPos = stripos($value, "@");
			$domain = $value;
			if ($domainPos !== false)
			{
				$domain = substr($value, $domainPos + 1);
			}
			else
			{
				$this->Message = $this->EmailAddressInvalidMessage;
				return false;
			}
			
			if ($this->PreventThrowawayAddresses || $this->CheckDNSRecords)
			{
				// determine if domain is in the blacklist
				if ($this->PreventThrowawayAddresses)
				{
					if (in_array($domain, $this->ThrowawayAddresses))
					{
						$this->Message = $this->DomainBlockedMessage;
						return false;
					}
				}
				
				// domain isn't, check DNS and see if the IP is blocked
				$recs = dns_get_record($domain);
				
				if (count($recs) === 0)
				{
					$this->Message = $this->EmailAddressInvalidMessage;
					return false;
				}
				
				$ips = array();
				foreach ($recs as $rec)
				{
					switch ($rec["type"])
					{
						case "AAAA":
						{
							$ips[] = $rec["ipv6"];
							break;
						}
						case "A":
						{
							$ips[] = $rec["ip"];
							break;
						}
					}
				}
				
				foreach ($this->ThrowawayAddresses as $ip)
				{
					if (in_array($ip, $ips))
					{
						$this->Message = $this->DomainBlockedMessage;
						return false;
					}
				}
			}
			return true;
		}
	}
	
	EmailAddressValidator::$DefaultThrowawayAddresses = array
	(
		// mailinator.com throwaway domains
		"mailinator.com",
		"mailinator.net",
		"streetwisemail.com",

		// mailinator.com throwaway IP addresses
		"2600:3c03::f03c:91ff:fe50:caa7",
		"23.239.11.30"
	);
	
	EmailAddressValidator::$DefaultDomainBlockedMessage = "Your e-mail address has been blocked";
	EmailAddressValidator::$DefaultEmailAddressInvalidMessage = "Please enter a valid e-mail address";
?>