<?php
/**
 * @package     Webmasterskaya\Joomla\MVC\Model
 * @subpackage
 *
 * @copyright   A copyright
 * @license     A "Slug" license name e.g. GPL2
 */

namespace Webmasterskaya\Joomla\MVC\Model;

use Psr\Http\Client\ClientInterface;

\defined('JPATH_PLATFORM') or die;


/**
 * Interface for a API model.
 */
interface APIModelInterface
{
	/**
	 * Method to get the HTTP Client object.
	 *
	 * @return ClientInterface
	 */
	public function getHTTPClient();
}