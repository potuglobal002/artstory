<?php

namespace Database\Seeders;

use App\Models\BlogPost;
use App\Models\Course;
use App\Models\JourneyItem;
use App\Models\SuccessStory;
use Illuminate\Database\Seeder;

class FrontendContentSeeder extends Seeder
{
    public function run(): void
    {
        if (Course::query()->doesntExist()) {
            $courses = [
                ['IELTS Academic', 'ielts-academic', 'IELTS', 'Most enrolled', 'The full four-skill course for students heading to university abroad.', '3 months', '36 classes', '7.0+', 'landing-assets/img/course-ielts-academic.svg'],
                ['PTE Academic', 'pte-academic', 'PTE', 'Fast track', 'Computer-based preparation for students who need a smart alternative pathway.', '2 months', '24 classes', '79+', 'landing-assets/img/course-pte-academic.svg'],
                ['IELTS Crash Program', 'ielts-crash-program', 'IELTS', 'Exam ready', 'Focused preparation for students who already have an exam date booked.', '5 weeks', '18 classes', '6.5+', 'landing-assets/img/course-ielts-crash.svg'],
                ['Junior English', 'junior-english', 'English', 'Kids', 'A confidence-building English program for younger learners.', 'Monthly', '3 days/week', 'Foundation', 'landing-assets/img/course-junior-english.svg'],
            ];

            foreach ($courses as $index => [$title, $slug, $category, $badge, $excerpt, $duration, $sessions, $score, $image]) {
                Course::query()->create([
                    'title' => $title,
                    'slug' => $slug,
                    'category' => $category,
                    'badge' => $badge,
                    'excerpt' => $excerpt,
                    'description' => '<p>' . e($excerpt) . '</p><p>Students receive guided classes, practice materials, feedback and progress tracking from the STS team.</p>',
                    'duration' => $duration,
                    'sessions' => $sessions,
                    'target_score' => $score,
                    'image_path' => $image,
                    'features' => [['title' => 'Guided practice', 'description' => 'Structured lessons with weekly feedback.']],
                    'outcomes' => [['text' => 'Understand the exam or skill path clearly.'], ['text' => 'Practice with measurable weekly targets.']],
                    'is_featured' => $index < 3,
                    'sort_order' => $index + 1,
                ]);
            }
        }

        if (JourneyItem::query()->doesntExist()) {
            foreach ([
                ['2019', 'Founded in Narsingdi', 'STS started with focused English and student guidance support.'],
                ['2021', 'Expanded test preparation', 'IELTS, PTE and spoken English programs became stronger learning tracks.'],
                ['2024', 'Recognized test venue journey', 'STS strengthened systems, rooms and student support for official exam readiness.'],
                ['2026', 'Dynamic digital platform', 'The website and admin modules now support scalable programs, blogs and stories.'],
            ] as $index => [$year, $title, $description]) {
                JourneyItem::query()->create([
                    'year' => $year,
                    'title' => $title,
                    'description' => $description,
                    'image_path' => 'landing-assets/img/glance-' . (($index % 3) + 1) . '.jpg',
                    'sort_order' => $index + 1,
                ]);
            }
        }

        if (SuccessStory::query()->doesntExist()) {
            foreach ([
                ['Nusrat Jahan', 'nusrat-jahan', 'IELTS Academic', 'Band 8.0', 'University pathway', 'STS helped me understand where my marks were going and how to improve.'],
                ['Tanvir Ahmed', 'tanvir-ahmed', 'IELTS Academic', 'Band 7.5', 'Writing improvement', 'The feedback sessions changed the way I planned and wrote essays.'],
                ['Sadia Islam', 'sadia-islam', 'Spoken English', 'Confidence built', 'Communication program', 'Regular speaking practice made English feel natural.'],
            ] as $index => [$name, $slug, $course, $result, $meta, $quote]) {
                SuccessStory::query()->create([
                    'name' => $name,
                    'slug' => $slug,
                    'course' => $course,
                    'result' => $result,
                    'meta' => $meta,
                    'quote' => $quote,
                    'image_path' => 'landing-assets/img/glance-' . (($index % 3) + 1) . '.jpg',
                    'is_featured' => true,
                    'sort_order' => $index + 1,
                ]);
            }
        }

        if (BlogPost::query()->doesntExist()) {
            foreach ([
                ['How to choose the right English program', 'right-english-program', 'Student Guide', 'A practical way to decide whether you need foundation, spoken English, IELTS or PTE.'],
                ['IELTS preparation mistakes students repeat', 'ielts-preparation-mistakes', 'IELTS', 'Common preparation habits that slow students down before test day.'],
                ['Why counselling matters before admission', 'why-counselling-matters', 'Admissions', 'A strong plan starts with knowing the learner level, timeline and goal.'],
            ] as $index => [$title, $slug, $category, $excerpt]) {
                BlogPost::query()->create([
                    'title' => $title,
                    'slug' => $slug,
                    'category' => $category,
                    'excerpt' => $excerpt,
                    'content' => '<p>' . e($excerpt) . '</p><p>This article can be fully edited from the Blogs module in the admin dashboard.</p>',
                    'image_path' => 'landing-assets/img/glance-' . (($index % 3) + 1) . '.jpg',
                    'published_at' => now()->subDays($index),
                    'is_featured' => $index === 0,
                    'sort_order' => $index + 1,
                ]);
            }
        }
    }
}
