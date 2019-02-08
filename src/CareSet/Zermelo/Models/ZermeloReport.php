<?php

namespace CareSet\Zermelo\Models;
use Illuminate\Support\Str;
use \Request;

abstract class ZermeloReport
{
	/**
	 * $_code
	 * Holds first optional parameter after the URI
	 * This is to help with selecting different groups or options
	 * Example: /Zermelo/ExampleReport/MyGroupCode
	 *
	 * @var string
	 */
	private $_code = null;

	/**
	 * $_parameters
	 * Holds additional optional paramters after the URI, after the $_code
	 * Example: /Zermelo/ExampleReport/MyGroupCode/MyOption1/MyOption2/MyOption3
	 *
	 * @var array
	 */
	private $_parameters = [];

	/**
	 * $_input
	 * Where GET and POST parameter submitted to the main display VIEW will be stored
	 *
	 * @var array
	 */
	private $_input = [];

	/**
	 * $_bolt_id
	 * If Report supports 'Bolting' system, the selected bolt_id will be stored
	 *
	 * @var boolean
	 */
	private $_bolt_id = false;

	private $_isCacheEnabled = null;

	private $_howLongToCacheInSeconds = null;


    /**
     * Should we enable the cache on this table?
     * This will improve the performance of very large and complex queries by only running the SQL once and then storing
     * the results in a dynamically creqted table in the _cache database.
     * But it also creates hard to debug update errors that are very confusing when changing GetSQL() contents.
     */
    protected $CACHE_ENABLED = true;


    /**
     * How much time should pass (in seconds) before you update your _cache table for this report?
     * this only has an effect when isCacheEnabled is turned on.
     */
    protected $HOW_LONG_TO_CACHE_IN_SECONDS = 600;


    /**
     * $INDICIES
     * The system will attempt to create an index out of these columns when creating the cache
     *
     * @var array
     */
    public $INDICIES = [];

	/**
	 * __construct
	 * Model will not attempt to parse URI or Request input
	 * This is up to the controller or pre-processing step to allow this
	 * model to be flexible.
	 * IMPORTANT: Code and Parameters are used to determine cache while $Input is NOT.
	 * a model with $Code = "Code", $Parameters = ["A","B"] will be cache separately then $Code="Code",$Paramters = ["A","C"]
	 * while $Input has no impact on caching
	 *
	 * @param string $Code - Optional parameter that can be used with model, usally through URI input
	 * @param array $Parameters - Optional additional parameters that can be used with model, usually through URI input
	 * @param array $Input - Additional optional parameters, usually through Request type input
	 * @return void
	 */
	public function __construct(?string $Code, array $Parameters = [], array $Input = [])
	{
		$this->_code = $Code;
		$this->_parameters = $Parameters;
		$this->_input = $Input;
		$this->setIsCacheEnabled( $this->CACHE_ENABLED );
		$this->setHowLongToCacheInSeconds( $this->HOW_LONG_TO_CACHE_IN_SECONDS );
	}


    /**
     * Get the URI key for the resource.
     *
     * @return string
     */
    public static function uriKey()
    {
    	return class_basename(get_called_class());
        // return Str::plural(Str::snake(class_basename(get_called_class()), '-'));
    }

    /**
     * Should we enable the cache on this table?
     * This will improve the performance of very large and complex queries by only running the SQL once and then storing
     * the results in a dynamically creqted table in the _cache database.
     * But it also creates hard to debug update errors that are very confusing when changing GetSQL() contents.
     */
    public function isCacheEnabled()
	{
        return $this->_isCacheEnabled;
    }

    public function setIsCacheEnabled( $isCacheEnabled )
	{
		$this->_isCacheEnabled = $isCacheEnabled;
	}


    /**
     * How much time should pass (in seconds) before you update your _cache table for this report?
     * this only has an effect when isCacheEnabled is turned on.
     */
    public function howLongToCacheInSeconds()
	{
        return $this->_howLongToCacheInSeconds; //ten minutes by default
    }

    /**
	 * @param null $howLongToCacheInSeconds
	 */
    public function setHowLongToCacheInSeconds( $howLongToCacheInSeconds )
    {
        $this->_howLongToCacheInSeconds = $howLongToCacheInSeconds;
    }
	
	/**
	 * getCode
	 * Retrieve code parameter used by model
	 *
	 * @return string
	 */
	public function getCode(): ?string
	{
		return $this->_code;
	}

    /**
     * @param $key
     * @return bool|mixed
	 *
	 * Get value of an input parameter by key, return false OW
     */
	public function getParameter( $key )
	{
		if ( isset( $this->_parameters[$key] ) ) {
            return $this->_parameters[ $key ];
        }

        return false;
	}

	/**
	 * getParameters
	 * Retrieve optional parameters used by model
	 *
	 * @return void
	 */
	public function getParameters(): ?array
	{
		return $this->_parameters;
	}
	/**
	 * getInput
	 * Retrieve optional inputs used by model
	 *
	 * @return void
	 */
	public function getInput($key = null)
	{
		$inputs = $this->_input;
		if($key!==null)
		{
			if(isset($inputs[$key]))
				return $inputs[$key];
			return null;
		}
		return $this->_input;
	}

    /**
     * @return array
	 *
	 * Merge the code with parameters
     */
	public function getMergedParameters()
	{
		$merged =  array_merge([$this->getCode()], $this->getParameters());
		return $merged;
	}

	/**
	 * GetSQL
	 * SQL statement to build the model based on optional $Code,$Parameters,$Input
	 * This will accept a single string SQL statement or an array of SQL statements
	 *
	 * @return array/string
	 */
	public function GetSQL()
	{
		return null;
	}
	/**
	 * MapRow
	 * When displaying to the tabular view,
	 * Model can chose to modify the content of each row cell.
	 * NOTE: Header name CAN be changed, but columns cannot be added or removed
	 *
	 * @param array $row
	 * @return void
	 */
	public function MapRow(array $row, int $row_number)
	{
		return $row;
	}

	/**
	 * OverrideHeader
	 * Override a default column format or add additional column tag to be sent back to the front end
	 * This returns the value as a reference parameter
	 *
	 * @param array &$format
	 * @param array &$tags
	 * @return void
	 */
	public function OverrideHeader(array &$format, array &$tags): void
	{

	}

	/**
	 * GetReportName
	 * Return the name of the report,
	 * By default, this will return the const $REPORT_NAME
	 * This function can be used to change the report name based on $code,$parameters,$input
	 *
	 * @return void
	 */
	public function GetReportName(): string
	{
		$me = get_called_class();
		return $me::REPORT_NAME;
	}
	/**
	 * GetReportDescription
	 * Return the description of the report,
	 * By default, this will return the const $DESCRIPTION.
	 * This function can be used to change the report description based on $code,$parameters,$input
	 * This supports returning HTML
	 *
	 * @return string
	 */
	public function GetReportDescription(): ?string
	{
		$me = get_called_class();
		return $me::DESCRIPTION;
	}



	public function SetBolt($id)
	{
		$this->_bolt_id = $id;
	}
	public function GetBoltId()
	{
		return $this->_bolt_id;
	}

	public function setRequestFormInput( $request_form_input )
	{
		$this->_request_form_input = $request_form_input;
	}

    /**
     * @param bool $json
     * @return array|string
	 *
	 * If json is true, return JSON, otherwise return parameter string
     */
	public function getRequestFormInput( $json = true )
	{
		if ( $json ) {
			return json_encode($this->_input);
		}

		return http_build_query( $this->_input );
	}



	/**
	 * GetClassName
	 * Return the Report Class Name
	 *
	 * @return void
	 */
	public function GetClassName(): string
	{
		return substr(strrchr(get_class($this), '\\'), 1);
	}

	/**
	 * getFileLocation
	 * Returns the actual path to the report file.
	 * This is used to determine the modify date to see if cache needs to be updated
	 *
	 * @return string
	 */
	public function getFileLocation(): string
	{
		$reflector = new \ReflectionClass(get_class($this));
		$fn = $reflector->getFileName();
		return $fn;
	}

}

?>
