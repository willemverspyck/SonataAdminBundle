<?php

declare(strict_types=1);

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Knp\Menu\MenuItem;
use Sonata\AdminBundle\DependencyInjection\Compiler\AliasDeprecatedPublicServicesCompilerPass;
use Sonata\AdminBundle\Menu\Matcher\Voter\ActiveVoter;
use Sonata\AdminBundle\Menu\Matcher\Voter\AdminVoter;
use Sonata\AdminBundle\Menu\Matcher\Voter\ChildrenVoter;
use Sonata\AdminBundle\Menu\MenuBuilder;
use Sonata\AdminBundle\Menu\Provider\GroupMenuProvider;
use Sonata\AdminBundle\Util\BCDeprecationParameters;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Loader\Configurator\ReferenceConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    // Use "service" function for creating references to services when dropping support for Symfony 4.4
    // Use "param" function for creating references to parameters when dropping support for Symfony 5.1
    $containerConfigurator->services()

        ->set('sonata.admin.menu_builder', MenuBuilder::class)
            // NEXT_MAJOR: Remove public and sonata.container.private tag.
            ->public()
            ->tag(AliasDeprecatedPublicServicesCompilerPass::PRIVATE_TAG_NAME, ['version' => '3.98'])
            ->args([
                new ReferenceConfigurator('sonata.admin.pool'),
                new ReferenceConfigurator('knp_menu.factory'),
                new ReferenceConfigurator('knp_menu.menu_provider'),
                new ReferenceConfigurator('event_dispatcher'),
            ])

        ->set('sonata.admin.sidebar_menu', MenuItem::class)
            // NEXT_MAJOR: Remove public and sonata.container.private tag.
            ->public()
            ->tag(AliasDeprecatedPublicServicesCompilerPass::PRIVATE_TAG_NAME, ['version' => '3.98'])
            ->tag('knp_menu.menu', ['alias' => 'sonata_admin_sidebar'])
            ->factory([
                new ReferenceConfigurator('sonata.admin.menu_builder'),
                'createSidebarMenu',
            ])

        ->set('sonata.admin.menu.group_provider', GroupMenuProvider::class)
            ->tag('knp_menu.provider')
            ->args([
                new ReferenceConfigurator('knp_menu.factory'),
                new ReferenceConfigurator('sonata.admin.pool'),
                new ReferenceConfigurator('security.authorization_checker'),
            ])

        ->set('sonata.admin.menu.matcher.voter.admin', AdminVoter::class)
            // NEXT_MAJOR: Remove public and sonata.container.private tag.
            ->public()
            ->tag(AliasDeprecatedPublicServicesCompilerPass::PRIVATE_TAG_NAME, ['version' => '3.98'])
            ->tag('knp_menu.voter')
            ->args([
                new ReferenceConfigurator('request_stack'),
            ])

        // NEXT_MAJOR: Remove this service.
        ->set('sonata.admin.menu.matcher.voter.children', ChildrenVoter::class)
            // NEXT_MAJOR: Remove public and sonata.container.private tag.
            ->public()
            ->tag(AliasDeprecatedPublicServicesCompilerPass::PRIVATE_TAG_NAME, ['version' => '3.98'])
            ->deprecate(...BCDeprecationParameters::forConfig(
                'The "%service_id%" service is deprecated since sonata-project/admin-bundle 3.28 and will be removed in 4.0.',
                '3.28'
            ))
            ->args([
                new ReferenceConfigurator('knp_menu.matcher'),
            ])

        ->set('sonata.admin.menu.matcher.voter.active', ActiveVoter::class)
            // NEXT_MAJOR: Remove public and sonata.container.private tag.
            ->public()
            ->tag(AliasDeprecatedPublicServicesCompilerPass::PRIVATE_TAG_NAME, ['version' => '3.98'])
            ->tag('knp_menu.voter');
};
