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

trait APIAwareTrait
{

	/**
	 * The HTTP client.
	 *
	 * @var    ClientInterface
	 */
	protected $_client;


	/**
	 * Get the HTTP client.
	 *
	 * @throws  \UnexpectedValueException
	 * @return  ClientInterface  The HTTP client.
	 */
	public function getHTTPClient()
	{
		if ($this->_client)
		{
			return $this->_client;
		}

		throw new \UnexpectedValueException('HTTP Client not set in ' . __CLASS__);
	}

	/**
	 * Set the database driver.
	 *
	 * @param   ClientInterface  $client  The HTTP client.
	 *
	 * @return  void
	 */
	public function setHTTPClient(ClientInterface $client = null)
	{
		$this->_client = $client;
	}
}