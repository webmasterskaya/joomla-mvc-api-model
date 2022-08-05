<?php
/**
 * @package     Webmasterskaya\Joomla\MVC\Model
 * @subpackage
 *
 * @copyright   A copyright
 * @license     A "Slug" license name e.g. GPL2
 */

namespace Webmasterskaya\Joomla\MVC\Model;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Http\HttpFactory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Factory\MVCFactoryAwareTrait;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\CMS\MVC\Factory\MVCFactoryServiceInterface;
use Joomla\CMS\MVC\Model\BaseModel;
use Joomla\Event\DispatcherAwareInterface;
use Joomla\Event\DispatcherAwareTrait;

abstract class BaseAPIModel extends BaseModel implements APIModelInterface, DispatcherAwareInterface
{
	use APIAwareTrait, MVCFactoryAwareTrait, DispatcherAwareTrait;

	/**
	 * The URL option for the component.
	 *
	 * @var    string
	 */
	protected $option = null;

	/**
	 * The event to trigger when cleaning cache.
	 *
	 * @var    string
	 */
	protected $event_clean_cache = null;

	/**
	 * Constructor
	 *
	 * @param   array                $config   An array of configuration options (name, state, client, client_options, client_adapters, ignore_request).
	 * @param   MVCFactoryInterface  $factory  The factory.
	 *
	 * @throws  \Exception
	 */
	public function __construct($config = array(), MVCFactoryInterface $factory = null)
	{
		parent::__construct($config);

		// Guess the option from the class name (Option)Model(View).
		if (empty($this->option))
		{
			$r = null;

			if (!preg_match('/(.*)Model/i', \get_class($this), $r))
			{
				throw new \Exception(Text::sprintf('JLIB_APPLICATION_ERROR_GET_NAME', __METHOD__), 500);
			}

			$this->option = ComponentHelper::getComponentName($this, $r[1]);
		}

		$this->setHTTPClient(
			\array_key_exists('client', $config)
				? $config['client']
				: HttpFactory::getHttp(
				$config['client_options'] ?? [], $config['client_adapters'] ?? null
			)
		);

		// Set the clean cache event
		if (isset($config['event_clean_cache']))
		{
			$this->event_clean_cache = $config['event_clean_cache'];
		}
		elseif (empty($this->event_clean_cache))
		{
			$this->event_clean_cache = 'onContentCleanCache';
		}

		if ($factory)
		{
			$this->setMVCFactory($factory);

			return;
		}

		$component = Factory::getApplication()->bootComponent($this->option);

		if ($component instanceof MVCFactoryServiceInterface)
		{
			$this->setMVCFactory($component->getMVCFactory());
		}
	}
}