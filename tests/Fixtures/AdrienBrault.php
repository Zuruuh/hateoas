<?php

declare(strict_types=1);

namespace Zuruuh\Hateoas\Tests\Fixtures;

use Symfony\Component\Serializer\Annotation as Serializer;
use Zuruuh\Hateoas\Mapping as Hateoas;
use Zuruuh\Hateoas\Relation\RelationsProviderInterface;

#[Hateoas\SelfRelation(
    href: 'http://adrienbrault.fr',
    exclusion: new Hateoas\Exclusion(
        groups: ['Default', 'simple'],
        excludeIf: "expr(object.firstName !== 'Adrien' || object.lastName !== 'Brault')"
    )
)]
final class AdrienBrault implements RelationsProviderInterface
{
    #[Serializer\Groups(['Default', 'simple'])]
    public string $firstName = 'Adrien';

    #[Serializer\Groups(['Default', 'simple'])]
    public string $lastName = 'Brault';

    #[Hateoas\Relation(
        name: 'computer',
        href: 'http://www.apple.com/macbook-pro/',
        exclusion: new Hateoas\Exclusion(groups: ['Default', 'simple']),
        embedded: new Hateoas\Embedded(
            content: 'expr(object.getMacbookPro())',
            type: Computer::class,
            exclusion: new Hateoas\Exclusion(groups: ['Default'])
        )
    )]
    public function getMacbookPro(): Computer
    {
        return new Computer('MacBook Pro');
    }

    #[Hateoas\Relation(name: 'broken-computer')]
    public function getWindowsComputer(): Computer
    {
        return new Computer('Windows Computer');
    }

    #[Hateoas\Relation(name: 'smartphone')]
    public function getiOSSmartphone(): Smartphone
    {
        return new Smartphone('iPhone 6');
    }

    #[Hateoas\Relation(name: 'smartphone')]
    public function getAndroidSmartphone(): Smartphone
    {
        return new Smartphone('Nexus 5');
    }

    #[Hateoas\RelationProvider]
    public static function provideHypermediaRelations(): array
    {
        return [
            new Hateoas\Relation('dynamic-relation', 'awesome!!!', ['wowowow']),
        ];
    }
}
