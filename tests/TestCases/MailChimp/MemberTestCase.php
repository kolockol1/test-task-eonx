<?php
declare(strict_types=1);

namespace Tests\App\TestCases\MailChimp;

use App\Database\Entities\MailChimp\MailChimpMember;
use Mailchimp\Mailchimp;

abstract class MemberTestCase extends ListTestCase
{
    protected $createdMembers = [];

    protected static $memberData = [
        'email_address' => 'test@test.com',
        'email_type' => 'text',
        'status' => 'subscribed',
        'merge_fields' => [],
        'interests' => [],
        'language' => 'ru',
        'vip' => false,
        'location' => [
            'latitude' => 55.55,
            'longitude' => 44.44,
        ],
        'marketing_permissions' => [
            'marketing_permission_id' => '123',
            'enabled' => true,
        ],
        'ip_signup' => '1.1.1.1',
        'timestamp_signup' => '1589827748',
        'ip_opt' => '2.2.2.2',
        'timestamp_opt' => '1589827749',
        'tags' => [],
    ];

    /**
     * Create MailChimp list into database.
     *
     * @param array $data
     *
     * @return MailChimpMember
     */
    protected function createMember(array $data): MailChimpMember
    {
        $member = new MailChimpMember($data);

        $this->entityManager->persist($member);
        $this->entityManager->flush();

        return $member;
    }

    public function tearDown(): void
    {
        /** @var Mailchimp $mailChimp */
        $mailChimp = $this->app->make(Mailchimp::class);

        foreach ($this->createdMembers as $member) {
            // Delete members on MailChimp after test
            $mailChimp->delete(\sprintf('lists/%s/members/%s', $member['listId'], $member['memberId']));
        }

        parent::tearDown();
    }
}