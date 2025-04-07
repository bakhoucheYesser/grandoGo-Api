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
 * Data Transfer Object for provider registration.
 */
class ProviderRegistrationDTO
{
    /**
     * User personal information.
     */
    #[Assert\Valid]
    #[Assert\NotBlank]
    public UserDTO $user;

    /**
     * Provider specific information.
     */
    #[Assert\Valid]
    public ?ProviderDTO $provider = null;

    /**
     * Vehicle information.
     */
    #[Assert\Valid]
    #[Assert\NotBlank]
    public VehicleDTO $vehicle;
}



