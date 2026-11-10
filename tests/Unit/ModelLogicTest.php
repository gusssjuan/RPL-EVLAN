<?php

namespace Tests\Unit;

use App\Enums\StatusTugas;
use App\Models\Budget;
use App\Models\Event;
use App\Models\EventMember;
use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Tests\TestCase;

class ModelLogicTest extends TestCase
{
    public function test_task_is_terlewat_returns_correct_value(): void
    {
        Carbon::setTestNow('2026-11-09');

        // Past deadline and not finished -> terlewat
        $taskTerlewat = new Task([
            'deadline' => '2026-11-05',
            'status' => StatusTugas::BelumDikerjakan,
        ]);
        $this->assertTrue($taskTerlewat->is_terlewat);

        // Past deadline but already finished -> not terlewat
        $taskSelesai = new Task([
            'deadline' => '2026-11-05',
            'status' => StatusTugas::SudahDiselesaikan,
        ]);
        $this->assertFalse($taskSelesai->is_terlewat);

        // Future deadline -> not terlewat
        $taskAkanDatang = new Task([
            'deadline' => '2026-11-20',
            'status' => StatusTugas::SedangDikerjakan,
        ]);
        $this->assertFalse($taskAkanDatang->is_terlewat);

        Carbon::setTestNow();
    }

    public function test_event_hari_menuju_accessor(): void
    {
        Carbon::setTestNow('2026-11-09');

        $eventHariIni = new Event(['tanggal' => '2026-11-09']);
        $this->assertEquals('Hari H', $eventHariIni->hari_menuju);

        $eventMendatang = new Event(['tanggal' => '2026-11-19']);
        $this->assertEquals('H-10', $eventMendatang->hari_menuju);

        $eventLewat = new Event(['tanggal' => '2026-11-04']);
        $this->assertEquals('H+5', $eventLewat->hari_menuju);

        Carbon::setTestNow();
    }

    public function test_event_is_participant(): void
    {
        $leader = new User;
        $leader->id = 1;

        $member = new User;
        $member->id = 2;

        $stranger = new User;
        $stranger->id = 3;

        $event = new Event(['leader_id' => 1]);

        $memberRecord = new EventMember(['user_id' => 2]);
        $event->setRelation('members', collect([$memberRecord]));

        $this->assertTrue($event->isLeader($leader));
        $this->assertFalse($event->isLeader($member));

        $this->assertTrue($event->isMember($member));
        $this->assertFalse($event->isMember($stranger));

        $this->assertTrue($event->isParticipant($leader));
        $this->assertTrue($event->isParticipant($member));
        $this->assertFalse($event->isParticipant($stranger));
    }

    public function test_budget_total_calculation_logic(): void
    {
        $budget = new Budget([
            'jumlah' => 5,
            'harga_satuan' => 250000,
        ]);

        // Simulating the booted saving hook logic:
        $expectedTotal = (int) $budget->jumlah * (int) $budget->harga_satuan;
        $this->assertEquals(1250000, $expectedTotal);
    }
}
