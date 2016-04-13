<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\SettingsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class SettingsListController. Is used for managing settings in General env.
 */
class SettingsDuplicateController extends Controller
{
    /**
     * Renders add setting form.
     *
     * @return Response
     */
    public function displayAction($name, $profile)
    {
        $setting = $this->get('ongr_settings.settings_manager')
                ->get($name, $profile);
        $profileManager = $this->get('ongr_settings.profiles_manager');
        $profiles = $profileManager->getAllProfiles();
        $params = [];
        $params['profiles'] = $profiles;
        $params['setting'] = $setting;
        $cache = $this->get('es.cache_engine');
        if ($cache->contains('settings_errors')) {
            $params['errors'] = $cache->fetch('settings_errors');
            $cache->delete('settings_errors');
        } elseif ($cache->contains('settings_success')) {
            $params['success'] = $cache->fetch('settings_success');
            $cache->delete('settings_success');
        }
        return $this->render(
            'ONGRSettingsBundle:Settings:duplicate.html.twig',
            $params
        );
    }
}
