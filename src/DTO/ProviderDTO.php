<?php

/*
 * This file is part of the GrandoGo project.
 *
 * (c) Yesser Bkhouch <yesserbakhouch@hotmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;


/**
 * DTO for provider data during registration.
 */
class ProviderDTO
{
    public ?string $companyName = null;

    public ?string $businessLicense = null;

    public ?string $taxId = null;

    #[Assert\Range(min: 1, max: 100)]
    public ?int $serviceAreaRadius = 30;
}
