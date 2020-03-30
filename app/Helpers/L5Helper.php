<?php

if (!function_exists('asset'))
{
	/**
	 * Generate an asset path for the application.
	 *
	 * @param  string  $path
	 * @param  bool    $secure
	 * @return string
	 */
	function asset($path, $secure = null)
	{
		return app('url')->asset($path, $secure);
	}
}

if (!function_exists('public_path'))
{
    /**
    * Return the path to public dir
    * @param null $path
    * @return string
    */
    function public_path($path=null)
    {
        return rtrim(app()->basePath('public/'.$path), '/');
    }
}

?>
