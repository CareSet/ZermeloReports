<?php

namespace CareSet\Zermelo\Models;
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


	private $_request_form_input = null;


	/**
	 * $VALID_COLUMN_FORMAT
	 * Valid Format a column header can be. This is used to validate OverrideHeader
	 *
	 * @var array
	 */
	public $VALID_COLUMN_FORMAT = ['TEXT','DETAIL','URL','CURRENCY','NUMBER','DECIMAL','DATE','DATETIME','TIME','PERCENT'];


	/**
	 * $DETAIL
	 * Header stub that will determine if a header is a 'SENTENCE' format
	 *
	 * @var array
	 */
	public $DETAIL     = ['Sentence'];

	/**
	 * $URL
	 * Header stub that will determine if a header is a 'URL' format
	 *
	 * @var array
	 */
	public $URL        = ['URL'];

	/**
	 * $CURRENCY
	 * Header stub that will determine if a header is a 'CURRENCY' format
	 *
	 * @var array
	 */
	public $CURRENCY   = ['Amt','Amount','Paid','Cost'];

	/**
	 * $NUMBER
	 * Header stub that will determine if a header is a 'NUMBER' format
	 *
	 * @var array
	 */
	public $NUMBER     = ['id','#','Num','Sum','Total','Cnt','Count'];

	/**
	 * $DECIMAL
	 * Header stub that will determine if a header is a 'DECIMAL' format
	 *
	 * @var array
	 */
	public $DECIMAL    = ['Avg','Average'];

	/**
	 * $PERCENT
	 * Header stub that will determine if a header is a 'PERCENTAGE' format
	 *
	 * @var array
	 */
	public $PERCENT    = ['Percent','Ratio','Perentage'];

	
	/**
	 * $SUGGEST_NO_SUMMARY
	 * This will mark the column that should not be used for statistical summary.
	 * Any column found with a a 'NO_SUMMARY' flag attached to its column header
	 *
	 * @var array
	 */
	public $SUGGEST_NO_SUMMARY = [];

	/**
	 * $SUBJECTS
	 * What the engine should consider as the 'noun' or 'subject'.
	 * This field will determine which field to be used as nodes on a graph
	 * 
	 * @var array
	 */
	public $SUBJECTS = [];

	/**
	 * $WEIGHTS
	 * What the engine should consider the weight between the subjects.
	 * This field is used to generate 'links' and link size between each nodes.
	 * 
	 * @var array
	 */
	public $WEIGHTS = [];



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
	public function MapRow(array $row)
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
	 * getReportDescription
	 * Return the description of the report,
	 * By default, this will return the const $DESCRIPTION.
	 * This function can be used to change the report description based on $code,$parameters,$input
	 *
	 * @return void
	 */
	public function getReportDescription(): ?string
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

	public function getRequestFormInput( $json = true )
	{
		if ( $json ) {
			return json_encode($this->_request_form_input);
		}

		return $this->_request_form_input;
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
