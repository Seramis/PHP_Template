<?php

/**
 * Class Tpl
 *
 * Super simple PHP template engine.
 * https://github.com/Seramis/PHP_Template
 *
 * Templates are written with alternative PHP syntax, so nothing new to learn.
 * Beginning and ending of php tags can be written as { and }
 * If letter after { is space, new line, tab, then it is not parsed. (Possibility to write JS in templates)
 *
 * If another template is needed to included, Tpl::incl($sTemplatePath, $aVars) can be used inside template:
 * {Tpl:incl('%path%/subpages/info.tpl', array('name' => 'blah'))}
 * Relative paths to current template file is supported.
 *
 * Example Template with JS code inside:
 * {foreach($aUsers as $aUser):}
 * 	{=$aUser['username']}
 * {endforeach;}
 * <script type="text/javascript">
 * if(true)
 * {
 * 	alert('a');
 * }
 * </script>
 */
class Tpl
{
	private $_sTplFile;
	private $_aData = array();
	private static $_sCompileDir = 'compile';
	public static $_bAlwaysCompile = true;

	/**
	 * Fetches given template file and given data.
	 *
	 * @param string $sTemplateFile
	 * @param array $aData
	 *
	 * @return string
	 */
	public static function fetchTemplate($sTemplateFile, $aData)
	{
		$oTpl = new self($sTemplateFile, $aData);
		return $oTpl->fetch();
	}

	/**
	 * Includes another template.
	 *
	 * @param string $sTemplateFile
	 * @param array $aData
	 */
	public static function incl($sTemplateFile, $aData = array())
	{
		$oTpl = new self($sTemplateFile, $aData);
		echo $oTpl->fetch();
	}

	/**
	 * Returns compiled file location. Generates compiled file, if needed.
	 *
	 * @param string $sTemplateFile
	 * @return string
	 */
	private static function getCompiled($sTemplateFile)
	{
		$sCompiledFile = static::getCompiledFilename($sTemplateFile);

		if(
			static::$_bAlwaysCompile
			|| !is_file($sCompiledFile)
			|| filemtime($sCompiledFile) < filemtime($sTemplateFile)
		)
		{
			static::compile($sTemplateFile);
		}

		return $sCompiledFile;
	}

	/**
	 * @param string $sTemplateFile
	 *
	 * @return string
	 */
	private static function compile($sTemplateFile)
	{
		$sCompiledFile = static::getCompiledFilename($sTemplateFile);
		$sDir = substr($sCompiledFile, 0, strrpos($sCompiledFile, DIRECTORY_SEPARATOR));

		$sTpl = file_get_contents($sTemplateFile);

		//Template data keywords and short echo syntax
		$sTpl = str_replace(
			array('%tpl%', '%path%', '%file%', '{='),
			array(
				$sTemplateFile,
				dirname($sTemplateFile),
				basename($sTemplateFile),
				'{echo '
			),
			$sTpl
		);

		//This is the magic!
		//Starts with {
		//Next letter IS NOT space, new lines, tab nor }
		//Everything except } (Including new lines, tabs etc.)
		//Ends with }
		$sTpl = preg_replace('#\{([^\s\r\n\t\}]+?[^\}]*?)\}#', '<?php $1; ?>', $sTpl);

		$sTpl = '<?php /*' . PHP_EOL
			. 'PHP Template - Joonatan Uusväli' . PHP_EOL
			. 'Compiled file of ' . $sTemplateFile . PHP_EOL
			. 'Compiled: ' . date('Y-m-d H:i:s') . PHP_EOL
			. '*/ ?>' . PHP_EOL . $sTpl;

		if(!is_dir($sDir))
		{
			mkdir($sDir, 0777, true);
		}

		file_put_contents($sCompiledFile, $sTpl);

		return true;
	}

	/**
	 * Generates compiled file location.
	 *
	 * @param string $sTemplateFile
	 * @return string
	 */
	private static function getCompiledFilename($sTemplateFile)
	{
		return static::$_sCompileDir . DIRECTORY_SEPARATOR . str_replace(array('\\', '/'), DIRECTORY_SEPARATOR, $sTemplateFile) . '.php';
	}

	/**
	 * @param null|string $sTemplateFile
	 * @param null|array $aData
	 */
	public function __construct($sTemplateFile = null, $aData = null)
	{
		if($sTemplateFile !== null)
		{
			$this->setTemplate($sTemplateFile);
		}

		if($aData !== null)
		{
			$this->setMany($aData);
		}
	}

	public function __set($sName, $mValue)
	{
		$this->_aData[$sName] = $mValue;
	}

	public function __get($sName)
	{
		if(!array_key_exists($sName, $this->_aData))
		{
			return null;
		}

		return $this->_aData[$sName];
	}

	/**
	 * @param array $aData
	 */
	public function setMany($aData)
	{
		foreach($aData as $sKey => $mValue)
		{
			$this->$sKey = $mValue;
		}
	}

	/**
	 * @param string $sTemplateFile
	 */
	public function setTemplate($sTemplateFile)
	{
		$this->_sTplFile = $sTemplateFile;
	}

	/**
	 * @param null|string $sTemplateFile
	 *
	 * @return string
	 */
	public function fetch($sTemplateFile = null)
	{
		if($sTemplateFile !== null)
		{
			$this->setTemplate($sTemplateFile);
		}
		$sCompiledFileName = static::getCompiled($this->_sTplFile);

		ob_start();

		extract($this->_aData);
		require($sCompiledFileName);

		return ob_get_clean();
	}
}