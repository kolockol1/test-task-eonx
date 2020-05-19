<?php
declare(strict_types=1);

namespace Tests\App\Unit\Http\Controllers\MailChimp;

use App\Http\Controllers\MailChimp\MembersController;
use Tests\App\TestCases\MailChimp\MemberTestCase;

class MembersControllerTest extends MemberTestCase
{
    /**
     * Test controller returns error response when exception is thrown during create MailChimp request.
     *
     * @return void
     */
    public function testCreateListMailChimpException(): void
    {
        $list = $this->generateList();
        /** @noinspection PhpParamsInspection Mock given on purpose */
        $controller = new MembersController($this->entityManager, $this->mockMailChimpForException('post'));

        $this->assertMailChimpExceptionResponse($controller->create($this->getRequest(static::$memberData), $list['list_id']));
    }

    /**
     * Test controller returns error response when exception is thrown during remove MailChimp request.
     *
     * @return void
     */
    public function testRemoveListMailChimpException(): void
    {
        /** @noinspection PhpParamsInspection Mock given on purpose */
        $controller = new MembersController($this->entityManager, $this->mockMailChimpForException('delete'));
        $list = $this->generateList();
        $member = $this->createMember(self::$memberData);
        $member->setMailChimpId('mail-chimp-id');

        $this->assertMailChimpExceptionResponse($controller->remove($list['list_id'], $member->getId()));
    }

    /**
     * Test controller returns error response when exception is thrown during update MailChimp request.
     *
     * @return void
     */
    public function testUpdateListMailChimpException(): void
    {
        /** @noinspection PhpParamsInspection Mock given on purpose */
        $controller = new MembersController($this->entityManager, $this->mockMailChimpForException('patch'));
        $list = $this->generateList();
        $member = $this->createMember(self::$memberData);
        $member->setMailChimpId('mail-chimp-id');

        $this->assertMailChimpExceptionResponse($controller->update($this->getRequest(), $list['list_id'], $member->getId()));
    }
}
