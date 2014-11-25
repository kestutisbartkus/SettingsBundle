<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\AdminBundle\Twig;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

class HiddenExtension extends \Twig_Extension
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @param ContainerInterface $container
     */
    public function __construct($container)
    {
        $this->container = $container;
        if ($container->isScopeActive('request')) {
            $this->request = $container->get('request');
        }
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction(
                'ongr_hidden',
                [$this, 'generate'],
                [
                    'needs_environment' => true,
                    'is_safe' => ['html']
                ]
            )
        ];
    }

    /**
     * @param \Twig_Environment $environment
     * @param array $data
     * @param bool $checkRequest to check if requst has
     * @return string
     */
    public function generate($environment, $data, $checkRequest = false)
    {
        if ($checkRequest && !is_null($this->request)) {
            foreach ($data as $name => $value) {
                $requestVal = $this->request->get($name);
                if (!isset($requestVal) || empty($requestVal) || is_null($requestVal)) {
                    unset($data[$name]);
                }
            }
        }
        return $environment->render(
            'ONGRAdminBundle:Utils:hidden.html.twig',
            ['data' => $this->modify($data)]
        );
    }

    /**
     * modifies data array for easier view rendering
     *
     * @param array $data data to modify
     * @return array
     */
    protected function modify($data)
    {
        $new = [];

        foreach ($data as $name => $value) {
            if (is_array($value)) {
                foreach ($value as $value2) {
                    if (!is_array($value2)) {
                        $new[] = [
                            'name' => $name."[]",
                            'value' => $value2
                        ];
                    }
                }
            } else {
                $new[] = [
                    'name' => $name,
                    'value' => $value
                ];
            }
        }
        return $new;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'ongr_hidden';
    }
}