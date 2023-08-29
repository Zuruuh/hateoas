<?php

declare(strict_types=1);

namespace Zuruuh\Hateoas\Tests\Fixtures;

use Symfony\Component\Serializer\Annotation as Serializer;
use Zuruuh\Hateoas\Mapping as Hateoas;
use Zuruuh\Hateoas\Relation\RelationsProviderInterface;

#[Hateoas\Relation(
    name: 'self',
    href: 'http://adrienbrault.fr',
    exclusion: new Hateoas\Exclusion(
        groups: ['Default', 'simple'],
        excludeIf: "expr(object.firstName !== 'Adrien' || object.lastName !== 'Brault')"
    )
)]
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
#[Hateoas\Relation(
    name: 'broken-computer',
    embedded: 'expr(object.getWindowsComputer())'
)]
#[Hateoas\Relation(
    name: 'smartphone',
    embedded: 'expr(object.getiOSSmartphone())'
)]
#[Hateoas\Relation(
    name: 'smartphone',
    embedded: 'expr(object.getAndroidSmartphone())'
)]
#[Hateoas\RelationProvider(method: 'getRelations')]
final class AdrienBrault implements RelationsProviderInterface
{
    #[Serializer\Groups(['Default', 'simple'])]
    public string $firstName = 'Adrien';

    #[Serializer\Groups(['Default', 'simple'])]
    public string $lastName = 'Brault';

    public function getMacbookPro(): Computer
    {
        return new Computer('MacBook Pro');
    }

    public function getWindowsComputer(): Computer
    {
        return new Computer('Windows Computer');
    }

    public function getiOSSmartphone(): Smartphone
    {
        return new Smartphone('iPhone 6');
    }

    public function getAndroidSmartphone(): Smartphone
    {
        return new Smartphone('Nexus 5');
    }

    public static function getRelations(): array
    {
        return [
            new Hateoas\Relation('dynamic-relation', 'awesome!!!', ['wowowow']),
        ];
    }
}
