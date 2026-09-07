<?php

namespace Database\Seeders;

use App\Models\Course;
use Illuminate\Database\Seeder;

class CourseDetailSeeder extends Seeder
{
    public function run(): void
    {
        $includes = [
            ['text' => 'Course book + STS practice pack'],
            ['text' => '4 full mock tests (LRW + Speaking)'],
            ['text' => 'Individual score review'],
            ['text' => 'IELTS registration support at our venue'],
            ['text' => 'Result day guidance'],
        ];

        $modules = [
            ['title' => 'Listening', 'description' => 'Section-by-section strategy, accent exposure, map and diagram labelling, note-completion traps.'],
            ['title' => 'Reading', 'description' => 'Skimming and scanning drills, True/False/Not Given logic, matching headings, paragraph-level paraphrase spotting and 60-minute pacing.'],
            ['title' => 'Writing Task 1', 'description' => 'Line graphs, bar charts, pie charts, tables, processes and maps. Overview writing, data selection and comparison language.'],
            ['title' => 'Writing Task 2', 'description' => 'Essay types, thesis and topic sentences, argument development, cohesion and the band descriptors you are actually marked against.'],
            ['title' => 'Speaking', 'description' => 'Part 1 fluency, Part 2 cue card structure, Part 3 abstract discussion. Recorded practice with playback feedback.'],
            ['title' => 'Mock & review', 'description' => 'Four full mock tests under exam conditions, each followed by an individual score breakdown session.'],
        ];

        $schedule = [
            ['batch' => 'Morning', 'days' => 'Sat Mon Wed', 'time' => '8:00 - 9:30 AM'],
            ['batch' => 'Afternoon', 'days' => 'Sun Tue Thu', 'time' => '4:00 - 5:30 PM'],
            ['batch' => 'Evening', 'days' => 'Sat Mon Wed', 'time' => '6:30 - 8:00 PM'],
        ];

        $faq = [
            ['question' => 'Do I need a certain level to join?', 'answer' => 'No. We take a short placement assessment first and suggest the right batch for your current level.'],
            ['question' => 'Can I take the exam at STS?', 'answer' => 'STS Institute supports IELTS registration and test venue guidance from the admission desk.'],
            ['question' => 'What if I miss classes?', 'answer' => 'Talk with the department coordinator. We help students recover missed lessons through support sessions where possible.'],
        ];

        Course::query()->each(function (Course $course) use ($includes, $modules, $schedule, $faq): void {
            $course->fill([
                'who_for' => $course->who_for ?: $this->whoFor($course),
                'lead' => $course->lead ?: $this->leadFor($course),
                'fee_note' => $course->fee_note ?: 'Instalments available - mock tests included',
                'batch_size' => $course->batch_size ?: '12-16 students',
                'campus' => $course->campus ?: 'Narsingdi Sadar',
                'includes' => $course->includes ?: $includes,
                'modules' => $course->modules ?: $modules,
                'schedule' => $course->schedule ?: $schedule,
                'faq' => $course->faq ?: $faq,
            ])->save();
        });
    }

    private function whoFor(Course $course): string
    {
        return match ($course->slug) {
            'ielts-academic' => 'You need IELTS for admission to a university in Australia, Canada, the UK or Europe, and you want a band above 6.5.',
            'ielts-crash-program' => 'You already have an exam date booked and need a focused, high-intensity preparation plan.',
            'pte-academic' => 'You want a computer-based English test pathway with guided practice and fast progress tracking.',
            'junior-english' => 'You want young learners to build English confidence through structured practice and friendly mentoring.',
            default => 'You need a clear learning path with guided classes, measurable practice and support from the STS team.',
        };
    }

    private function leadFor(Course $course): string
    {
        return match ($course->slug) {
            'ielts-academic' => 'Academic IELTS is not an English exam. It is a test of how you handle academic English under a clock. This course trains both - the language and the clock.',
            'ielts-crash-program' => 'A compact exam-readiness program for students who need sharp strategy, timed practice and quick feedback.',
            'pte-academic' => 'A practical PTE pathway built around computer-based tasks, scoring patterns and repeated performance review.',
            'junior-english' => 'A confidence-building English program for young learners who need speaking, vocabulary and classroom participation support.',
            default => $course->excerpt ?: 'A guided STS Institute program with structured classes, practice support and measurable progress.',
        };
    }
}
