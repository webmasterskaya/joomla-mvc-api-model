<?php
/**
 * @package     Webmasterskaya\Joomla\MVC\Model
 * @subpackage
 *
 * @copyright   A copyright
 * @license     A "Slug" license name e.g. GPL2
 */

namespace Webmasterskaya\Joomla\MVC\Model;

use Joomla\CMS\MVC\Factory\MVCFactoryAwareTrait;
use Joomla\CMS\MVC\Model\BaseModel;
use Joomla\Event\DispatcherAwareInterface;
use Joomla\Event\DispatcherAwareTrait;

abstract class BaseAPIModel extends BaseModel implements APIModelInterface, DispatcherAwareInterface
{
	use APIAwareTrait, MVCFactoryAwareTrait, DispatcherAwareTrait;
}