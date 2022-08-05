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
use Joomla\CMS\Extension\ComponentInterface;
use Joomla\CMS\Factory;
use Joomla\CMS\Http\HttpFactory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Factory\MVCFactoryAwareTrait;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\CMS\MVC\Factory\MVCFactoryServiceInterface;
use Joomla\CMS\MVC\Model\BaseModel;
use Joomla\CMS\Uri\Uri;
use Joomla\Event\DispatcherAwareInterface;
use Joomla\Event\DispatcherAwareTrait;
use Joomla\Event\DispatcherInterface;
use Joomla\Event\EventInterface;

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

	protected $__endpoint;

	protected $endpoint;

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

		if (!empty($config['endpoint']))
		{
			$this->endpoint = $config['endpoint'];
		}

		$component = Factory::getApplication()->bootComponent($this->option);

		if ($component instanceof MVCFactoryServiceInterface)
		{
			$this->setMVCFactory($component->getMVCFactory());
		}
	}

	protected function _createEndpoint($endpoint)
	{


		if (\is_array($endpoint) || \is_string($endpoint))
		{
			$uri = new URI();

			if (\is_string($endpoint))
			{
				$uri->parse($endpoint);

				return $uri;
			}

			if (count($endpoint) < 2)
			{
				throw new \ArgumentCountError('Array of endpoint params must have a 2 items, given ' . count($endpoint));
			}

			$uri->setHost($endpoint[0]);
			$uri->setPath($endpoint[1]);

			return $uri;
		}

		if ($endpoint instanceof URI)
		{
			return $endpoint;
		}

		throw new \InvalidArgumentException('Unsupported endpoint type', 0);
	}

	public function getEndpoint($endpoint = null)
	{
		if (empty($endpoint) && empty($this->__endpoint))
		{
			$endpoint = $this->__endpoint;
		}

		if (!empty($endpoint) && !!($_endpoint = $this->_createEndpoint($endpoint)))
		{
			return $_endpoint;
		}

		throw new \Exception('Endpoint not set', 0);
	}

	/**
	 * Boots the component with the given name.
	 *
	 * @param   string  $component  The component name, eg. com_content.
	 *
	 * @throws \Exception
	 * @return  ComponentInterface  The service container
	 */
	protected function bootComponent($component): ComponentInterface
	{
		return Factory::getApplication()->bootComponent($component);
	}

	/**
	 * Dispatches the given event on the internal dispatcher, does a fallback to the global one.
	 *
	 * @param   EventInterface  $event  The event
	 *
	 * @return  void
	 */
	protected function dispatchEvent(EventInterface $event)
	{
		try
		{
			$this->getDispatcher()->dispatch($event->getName(), $event);
		}
		catch (\UnexpectedValueException $e)
		{
			Factory::getContainer()->get(DispatcherInterface::class)->dispatch($event->getName(), $event);
		}
	}
}