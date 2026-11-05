<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * The last KPK Config screens VMS did not have:
 *   location_visited.meeting_room  KPK LocationVisitedLookup.meetingroom - the rooms Meeting Session offers
 *   meeting_sessions               KPK SAFEG_MTG_SESSION_LOOKUP (room, date, start, end, number of visitors)
 *   video_questions / _answers     KPK SAFEG_TR_QUES + SAFEG_TR_QUES_COLL (questions per training video,
 *                                  sequence, answer options with the correct one)
 *   dashboard_uploads              KPK SAFEG_DASH_UPLOAD (picture shown on the dashboard)
 * The questionnaire threshold (KPK SAFEG_TR_CONF) is a settings row per client. Role Management gets
 * Config > Meeting Session / Questionnaire / Dashboard Upload, ticked for roles that already have License Class,
 * Vehicle Type or Reject Reason. Nothing is seeded.
 */
class KpkMeetingQuestionnaireDashboard extends Migration
{
    private function make(string $table, array $fields, array $keys = []): void
    {
        if ($this->db->tableExists($table)) {
            return;
        }
        $this->forge->addField($fields + [
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        foreach ($keys as $k) {
            $this->forge->addKey($k);
        }
        $this->forge->createTable($table);
    }

    public function up()
    {
        if ($this->db->tableExists('location_visited') && ! $this->db->fieldExists('meeting_room', 'location_visited')) {
            $this->forge->addColumn('location_visited', ['meeting_room' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0]]);
        }

        $id     = ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true];
        $client = ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true];
        $status = ['type' => 'ENUM', 'constraint' => ['active', 'inactive'], 'default' => 'active'];

        $this->make('meeting_sessions', [
            'id' => $id, 'client_id' => $client,
            'location_visited_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'session_date'        => ['type' => 'DATE'],
            'start_time'          => ['type' => 'TIME'],
            'end_time'            => ['type' => 'TIME'],
            'num_visitor'         => ['type' => 'INT', 'constraint' => 5, 'unsigned' => true, 'default' => 5],
            'num_visitor_reserve' => ['type' => 'INT', 'constraint' => 5, 'unsigned' => true, 'default' => 0],
            'status' => $status,
        ], ['client_id', 'session_date']);

        $this->make('video_questions', [
            'id' => $id, 'client_id' => $client,
            'video_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'question' => ['type' => 'VARCHAR', 'constraint' => 500],
            'sequence' => ['type' => 'INT', 'constraint' => 5, 'unsigned' => true, 'default' => 1],
            'status'   => $status,
            'created_by' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
        ], ['client_id', 'video_id']);

        $this->make('video_question_answers', [
            'id' => $id,
            'question_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'answer'      => ['type' => 'VARCHAR', 'constraint' => 300],
            'is_correct'  => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'sequence'    => ['type' => 'INT', 'constraint' => 5, 'unsigned' => true, 'default' => 1],
        ], ['question_id']);

        $this->make('dashboard_uploads', [
            'id' => $id, 'client_id' => $client,
            'name'        => ['type' => 'VARCHAR', 'constraint' => 200],
            'file_path'   => ['type' => 'VARCHAR', 'constraint' => 255],
            'mime'        => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'status'      => $status,
            'uploaded_by' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
        ], ['client_id']);

        if (! $this->db->tableExists('roles') || ! $this->db->fieldExists('access', 'roles')) {
            return;
        }
        foreach ($this->db->table('roles')->select('id, access')->get()->getResultArray() as $role) {
            $access = json_decode((string) ($role['access'] ?? ''), true);
            if (! is_array($access) || empty($access)) {
                continue;
            }
            $config = (array) ($access['config'] ?? []);
            if (empty($config['license_class']) && empty($config['vehicle_type']) && empty($config['reject_reason'])) {
                continue;
            }
            $changed = false;
            foreach (['meeting_session', 'questionnaire', 'dashboard_upload'] as $k) {
                if (! array_key_exists($k, $config)) {
                    $access['config'][$k] = true;
                    $changed = true;
                }
            }
            if ($changed) {
                $this->db->table('roles')->where('id', $role['id'])->update(['access' => json_encode($access)]);
            }
        }
    }

    public function down()
    {
        foreach (['meeting_sessions', 'video_question_answers', 'video_questions', 'dashboard_uploads'] as $t) {
            $this->forge->dropTable($t, true);
        }
        if ($this->db->tableExists('location_visited') && $this->db->fieldExists('meeting_room', 'location_visited')) {
            $this->forge->dropColumn('location_visited', 'meeting_room');
        }
    }
}
