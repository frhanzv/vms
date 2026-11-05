<?php

namespace App\Controllers;

/**
 * Config > Questionnaire (KPK questionnaire-conf / training-vid-conf "Go To Questionnaire"):
 * questions for each training video (Config > Video Management), with a sequence, a status and
 * answer options where one or more are ticked as correct, plus the Question Threshold
 * (KPK SAFEG_TR_CONF). A client admin manages its own questions; the platform admin manages the
 * shared ones (client_id NULL). Routes: config/questionnaire[/(:num)|/save|/(:num)/toggle|/threshold].
 */
class VideoQuestionConfig extends BaseController
{
    private const PAGE_SIZES = [10, 20, 30, 40];

    private function scope(): ?int
    {
        helper(['role', 'feature']);

        return is_platform_superadmin() ? null : (int) current_client_id();
    }

    private function json(bool $ok, string $msg, array $extra = [], int $code = 200)
    {
        return $this->response->setStatusCode($code)->setJSON(['success' => $ok, 'message' => $msg] + $extra);
    }

    private function allowed(): bool
    {
        helper(['access']);

        return has_access('config', 'questionnaire');
    }

    private function ready(): bool
    {
        $db = \Config\Database::connect();

        return $db->tableExists('video_questions') && $db->tableExists('video_question_answers');
    }

    /** Training videos this admin may attach questions to: id => name. */
    private function videos(): array
    {
        $db = \Config\Database::connect();
        if (! $db->tableExists('videos')) {
            return [];
        }
        $b = $db->table('videos')->select('id, name')->orderBy('name');
        if (($c = $this->scope()) !== null && $db->fieldExists('client_id', 'videos')) {
            $b->groupStart()->where('client_id', $c)->orWhere('client_id IS NULL', null, false)->orWhere('client_id', 0)->groupEnd();
        }

        return array_column($b->get()->getResultArray(), 'name', 'id');
    }

    private function mine(int $id): ?array
    {
        $b = \Config\Database::connect()->table('video_questions')->where('id', $id);
        $c = $this->scope();
        $c === null ? $b->where('client_id IS NULL', null, false) : $b->where('client_id', $c);

        return $b->get()->getRowArray() ?: null;
    }

    private function thresholdKey(): string
    {
        return 'questionnaire_threshold_' . ($this->scope() ?? 0);
    }

    public function index()
    {
        if (! $this->allowed()) {
            return $this->json(false, 'Not allowed.', [], 403);
        }
        if (! $this->ready()) {
            return $this->json(false, 'Run php spark migrate first.', ['data' => [], 'total' => 0, 'pages' => 1, 'page' => 1, 'videos' => []]);
        }
        $db      = \Config\Database::connect();
        $c       = $this->scope();
        $q       = trim((string) $this->request->getGet('q'));
        $video   = (int) $this->request->getGet('video_id');
        $page    = max(1, (int) $this->request->getGet('page'));
        $perPage = in_array((int) $this->request->getGet('per_page'), self::PAGE_SIZES, true) ? (int) $this->request->getGet('per_page') : self::PAGE_SIZES[0];

        $apply = function ($b) use ($c, $q, $video) {
            $c === null ? $b->where('client_id IS NULL', null, false)
                : $b->groupStart()->where('client_id', $c)->orWhere('client_id IS NULL', null, false)->groupEnd();
            if ($q !== '') {
                $b->like('question', $q);
            }
            if ($video > 0) {
                $b->where('video_id', $video);
            }

            return $b;
        };
        $total  = $apply($db->table('video_questions'))->countAllResults();
        $pages  = max(1, (int) ceil($total / $perPage));
        $page   = min($page, $pages);
        $rows   = $apply($db->table('video_questions'))->orderBy('video_id')->orderBy('sequence')->orderBy('id')->limit($perPage, ($page - 1) * $perPage)->get()->getResultArray();
        $videos = $this->videos();
        $counts = [];
        if ($rows) {
            foreach ($db->table('video_question_answers')->select('question_id, COUNT(*) n, SUM(is_correct) ok')->whereIn('question_id', array_column($rows, 'id'))->groupBy('question_id')->get()->getResultArray() as $r) {
                $counts[$r['question_id']] = [(int) $r['n'], (int) $r['ok']];
            }
        }
        foreach ($rows as &$r) {
            $r['video_name'] = $videos[$r['video_id']] ?? '-';
            [$r['answers'], $r['correct']] = $counts[$r['id']] ?? [0, 0];
            $r['editable'] = $c === null ? $r['client_id'] === null : (int) $r['client_id'] === $c;
        }

        return $this->json(true, 'ok', ['data' => $rows, 'total' => $total, 'pages' => $pages, 'page' => $page, 'per_page' => $perPage,
            'videos' => $videos, 'page_sizes' => self::PAGE_SIZES]);
    }

    public function show($id)
    {
        if (! $this->allowed() || ! $this->ready()) {
            return $this->json(false, 'Not found.', [], 404);
        }
        $db  = \Config\Database::connect();
        $row = $db->table('video_questions')->where('id', (int) $id)->get()->getRowArray();
        $c   = $this->scope();
        if (! $row || ($c !== null && $row['client_id'] !== null && (int) $row['client_id'] !== $c)) {
            return $this->json(false, 'Question not found.', [], 404);
        }
        $row['answer_list'] = $db->table('video_question_answers')->where('question_id', (int) $id)->orderBy('sequence')->orderBy('id')->get()->getResultArray();

        return $this->json(true, 'ok', ['data' => $row]);
    }

    public function save()
    {
        if (! $this->allowed() || ! $this->ready()) {
            return $this->json(false, 'Not allowed.', [], 403);
        }
        $p        = $this->request->getPost();
        $id       = (int) ($p['id'] ?? 0);
        $question = trim((string) ($p['question'] ?? ''));
        $video    = (int) ($p['video_id'] ?? 0);
        $seq      = trim((string) ($p['sequence'] ?? ''));
        $status   = ($p['status'] ?? 'active') === 'inactive' ? 'inactive' : 'active';
        if ($question === '') {
            return $this->json(false, 'Question is required.', [], 400);
        }
        if (! isset($this->videos()[$video])) {
            return $this->json(false, 'Choose the Training Video.', [], 400);
        }
        if ($seq === '' || ! ctype_digit($seq) || (int) $seq > 9999) {
            return $this->json(false, 'Sequence is required (a number).', [], 400);
        }
        $answers = [];
        $correct = array_map('intval', (array) ($p['correct'] ?? []));
        foreach (array_values((array) ($p['answers'] ?? [])) as $i => $a) {
            $a = trim((string) $a);
            if ($a !== '') {
                $answers[] = ['answer' => mb_substr($a, 0, 300), 'is_correct' => in_array($i, $correct, true) ? 1 : 0];
            }
        }
        if (count($answers) < 2) {
            return $this->json(false, 'Add at least two answers.', [], 400);
        }
        if (! array_filter(array_column($answers, 'is_correct'))) {
            return $this->json(false, 'Tick the correct answer.', [], 400);
        }

        $db  = \Config\Database::connect();
        $now = date('Y-m-d H:i:s');
        $data = ['video_id' => $video, 'question' => mb_substr($question, 0, 500), 'sequence' => (int) $seq, 'status' => $status, 'updated_at' => $now];
        $db->transStart();
        if ($id) {
            if (! $this->mine($id)) {
                $db->transComplete();

                return $this->json(false, 'Question not found.', [], 404);
            }
            $db->table('video_questions')->where('id', $id)->update($data);
            $db->table('video_question_answers')->where('question_id', $id)->delete();
        } else {
            $db->table('video_questions')->insert($data + ['client_id' => $this->scope(), 'created_by' => (int) session()->get('user_id') ?: null, 'created_at' => $now]);
            $id = (int) $db->insertID();
        }
        foreach ($answers as $i => $a) {
            $db->table('video_question_answers')->insert($a + ['question_id' => $id, 'sequence' => $i + 1, 'created_at' => $now, 'updated_at' => $now]);
        }
        $db->transComplete();

        return $this->json(true, 'Question saved.');
    }

    public function toggle($id)
    {
        if (! $this->allowed() || ! $this->ready() || ! ($row = $this->mine((int) $id))) {
            return $this->json(false, 'Question not found.', [], 404);
        }
        $new = $row['status'] === 'active' ? 'inactive' : 'active';
        \Config\Database::connect()->table('video_questions')->where('id', (int) $id)->update(['status' => $new, 'updated_at' => date('Y-m-d H:i:s')]);

        return $this->json(true, 'Question is now ' . ucfirst($new) . '.');
    }

    /** KPK Question Threshold: GET reads it, POST saves it (one value per client). */
    public function threshold()
    {
        if (! $this->allowed()) {
            return $this->json(false, 'Not allowed.', [], 403);
        }
        $db  = \Config\Database::connect();
        $key = $this->thresholdKey();
        if ($this->request->getMethod() === 'POST' || strtolower($this->request->getMethod()) === 'post') {
            $v = trim((string) $this->request->getPost('threshold'));
            if ($v === '' || ! ctype_digit($v) || (int) $v > 9999) {
                return $this->json(false, 'Threshold must be a whole number.', [], 400);
            }
            $now = date('Y-m-d H:i:s');
            $db->table('settings')->where('setting_key', $key)->countAllResults() > 0
                ? $db->table('settings')->where('setting_key', $key)->update(['setting_value' => (string) (int) $v, 'updated_at' => $now])
                : $db->table('settings')->insert(['setting_key' => $key, 'setting_value' => (string) (int) $v, 'created_at' => $now, 'updated_at' => $now]);

            return $this->json(true, 'Question threshold saved.');
        }
        $row = $db->table('settings')->where('setting_key', $key)->get()->getRowArray();

        return $this->json(true, 'ok', ['threshold' => (int) ($row['setting_value'] ?? 0)]);
    }
}
