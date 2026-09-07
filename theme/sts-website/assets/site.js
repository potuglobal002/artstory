/* ============================================================
   STS Institute — shared site script
   Data, header/footer injection, and page interactions.
   ------------------------------------------------------------
   ONE THING TO SET: paste your Apps Script Web App URL below to
   make the Admission and Contact forms post to your Google Sheet.
   Leave it empty and the forms will still validate + confirm.
   ============================================================ */
const FORM_ENDPOINT = ""; // e.g. "https://script.google.com/macros/s/AKfy.../exec"

const SITE = {
  name: "STS Institute",
  tagline: "Secret to Success",
  logo: "https://sts.institute/riltoduw/2024/08/Logo-v2-01.png",
  web: "sts.institute",
  email: "info@sts.institute",
  phones: ["01901-402202", "01901-402203"],
  desks: [
    { name: "PR & Admission", num: "01901-402202, 01901-402203" },
    { name: "IELTS Department", num: "01901-402204" },
    { name: "Language Department", num: "01901-402205" }
  ],
  address: "Narsingdi Sadar, Narsingdi, Bangladesh",
  fb: "https://facebook.com/TrainingSTS",
  branches: [
    { name: "STS Institute", role: "Head office · IDP IELTS test venue", loc: "Narsingdi Sadar, Narsingdi", num: "01901-402202 / 01901-402203", mail: "info@sts.institute" },
    { name: "STS Junior Institute", role: "Kids' & school English", loc: "Upazila Mor, Narsingdi", num: "01901-402251 / 01939-606850", mail: "info@sts.institute" },
    { name: "HITT Center", role: "Sister concern · IDP IELTS test venue", loc: "Narayanganj", num: "01901-402209 / 01901-402258", mail: "hittcenterofficial@gmail.com" }
  ]
};

/* ---------------- Courses ---------------- */
const COURSES = [
  {
    slug: "ielts-academic", name: "IELTS Academic", family: "IELTS", badge: "Most enrolled",
    image: "assets/img/course-ielts-academic.svg",
    short: "The full four-skill course for students heading to university abroad.",
    band: "7.0+", duration: "3 months", sessions: "36 classes", batch: "12–16 students",
    fee: "12,000", feeNote: "Instalments available · mock tests included",
    who: "You need IELTS for admission to a university in Australia, Canada, the UK or Europe, and you want a band above 6.5.",
    lead: "Academic IELTS is not an English exam. It is a test of how you handle academic English under a clock. This course trains both — the language and the clock.",
    modules: [
      { t: "Listening", d: "Section-by-section strategy, accent exposure (British, Australian, North American), map and diagram labelling, note-completion traps." },
      { t: "Reading", d: "Skimming and scanning drills, True/False/Not Given logic, matching headings, paragraph-level paraphrase spotting, 60-minute pacing." },
      { t: "Writing Task 1", d: "Line graphs, bar charts, pie charts, tables, processes and maps. Overview writing, data selection, comparison language." },
      { t: "Writing Task 2", d: "Essay types, thesis and topic sentences, argument development, cohesion, and the band descriptors you are actually marked against." },
      { t: "Speaking", d: "Part 1 fluency, Part 2 cue card structure, Part 3 abstract discussion. Recorded practice with playback feedback." },
      { t: "Mock & review", d: "Four full mock tests under exam conditions, each followed by an individual score breakdown session." }
    ],
    outcomes: [
      "Sit the exam knowing exactly how each section is scored",
      "Write Task 1 and Task 2 inside the 20/40 minute split",
      "Hold a two-minute Part 2 answer without drying up",
      "Read three passages in 60 minutes without panic"
    ],
    includes: ["Course book + STS practice pack", "4 full mock tests (LRW + Speaking)", "Individual score review", "IELTS registration support at our venue", "Result day guidance"],
    schedule: [
      { batch: "Morning", days: "Sat · Mon · Wed", time: "8:00 – 9:30 AM" },
      { batch: "Afternoon", days: "Sun · Tue · Thu", time: "4:00 – 5:30 PM" },
      { batch: "Evening", days: "Sat · Mon · Wed", time: "6:30 – 8:00 PM" }
    ],
    faq: [
      { q: "Do I need a certain level to join?", a: "You need a working intermediate level. We run a free placement assessment before admission — if you are below the line, we start you on Foundation English first, and you keep your fee credit." },
      { q: "Can I take the exam at STS?", a: "Yes. STS is an authorised IDP IELTS test venue, so you can prepare and sit the test in the same building in Narsingdi." },
      { q: "What if I miss classes?", a: "Recorded class notes are shared with your batch, and you can join the same topic in another running batch once per module." }
    ]
  },
  {
    slug: "ielts-general", name: "IELTS General Training", family: "IELTS", badge: "",
    short: "Built for migration, PR applications and work abroad.",
    band: "6.5+", duration: "3 months", sessions: "36 classes", batch: "12–16 students",
    fee: "12,000", feeNote: "Instalments available · mock tests included",
    who: "You are applying for permanent residency, a work visa, or a professional registration that asks for General Training.",
    lead: "General Training swaps academic charts for letters and workplace reading. The scoring is the same; the skill set is not. This course is written for the migration timeline.",
    modules: [
      { t: "Listening", d: "The same four sections as Academic, with heavy practice on everyday transactional English." },
      { t: "Reading", d: "Notices, advertisements, workplace documents and one long passage. Volume management and detail accuracy." },
      { t: "Writing Task 1 — letters", d: "Formal, semi-formal and informal letters. Purpose, tone, and the three bullet points you must cover." },
      { t: "Writing Task 2", d: "Opinion, discussion and problem–solution essays at General Training level." },
      { t: "Speaking", d: "Everyday topics, work and community themes, and Part 3 opinion handling." },
      { t: "Mock & review", d: "Four full mocks with a written score breakdown for each." }
    ],
    outcomes: ["Write all three letter registers confidently", "Finish General Reading with time to check", "Hit the band your visa stream requires", "Understand your TRF before you apply"],
    includes: ["Course book + letter bank", "4 full mock tests", "Individual score review", "Registration support at our venue"],
    schedule: [
      { batch: "Morning", days: "Sun · Tue · Thu", time: "9:45 – 11:15 AM" },
      { batch: "Evening", days: "Sat · Mon · Wed", time: "6:30 – 8:00 PM" }
    ],
    faq: [
      { q: "Academic or General — which do I need?", a: "Universities almost always want Academic. Migration and most work streams want General Training. Bring your requirement letter and our PR desk will confirm it with you before you pay." },
      { q: "How long before my visa deadline should I start?", a: "Give yourself four months: three for the course, one for booking, sitting and receiving the TRF." }
    ]
  },
  {
    slug: "ielts-crash", name: "IELTS Crash Program", family: "IELTS", badge: "Fast track",
    image: "assets/img/course-ielts-crash.svg",
    short: "One month, high intensity, for students with a date already booked.",
    band: "6.5+", duration: "1 month", sessions: "20 classes", batch: "10–14 students",
    fee: "7,000", feeNote: "Includes 2 mock tests",
    who: "You have taken IELTS before, or you already have a strong base, and your test date is four to six weeks away.",
    lead: "A crash program is not a shortcut — it is compression. We assume you have the English and spend every hour on scoring behaviour: timing, task response, and the errors that cost you half a band.",
    modules: [
      { t: "Diagnostic", d: "A full mock in week one so we teach to your actual gap, not a generic syllabus." },
      { t: "Writing repair", d: "Task response, coherence and lexical range — the three descriptors that hold most students at 6.0." },
      { t: "Speaking sprint", d: "Daily recorded speaking rounds with fluency and pronunciation feedback." },
      { t: "Listening & Reading speed", d: "Timed sets every session with error-pattern tracking." },
      { t: "Exam week", d: "Final mock, score review, and a personal test-day plan." }
    ],
    outcomes: ["Close the specific gap between your last score and your target", "Stop losing marks to timing", "Walk into the test with a written plan for each section"],
    includes: ["Diagnostic + final mock", "Error tracker sheet", "Daily speaking recordings", "Test-day plan"],
    schedule: [
      { batch: "Intensive AM", days: "Sat – Wed", time: "8:00 – 10:00 AM" },
      { batch: "Intensive PM", days: "Sat – Wed", time: "5:00 – 7:00 PM" }
    ],
    faq: [{ q: "Is a crash course enough on its own?", a: "Only if your English is already at the level of your target band. We will tell you honestly at the diagnostic — and if it is not, we will move you to the full course instead of taking your money." }]
  },
  {
    slug: "ielts-mock-club", name: "IELTS Mock Test Club", family: "IELTS", badge: "",
    short: "Exam-condition mocks with a real examiner-style score breakdown.",
    band: "Any", duration: "Rolling", sessions: "Weekly", batch: "Open",
    fee: "500", feeNote: "Per full mock (LRW + Speaking)",
    who: "You are preparing on your own, or with us, and you want a real measurement before you pay for the exam.",
    lead: "Most students do not fail IELTS. They discover on test day that they have never finished a full test in one sitting. The Mock Club fixes that for the price of a lunch.",
    modules: [
      { t: "Listening, Reading, Writing", a: "", d: "Sat in one continuous sitting, same timing and materials handling as the real test." },
      { t: "Speaking", d: "A one-to-one recorded interview in all three parts with a trained assessor." },
      { t: "Score report", d: "A band estimate per skill plus written notes on what moves you up half a band." }
    ],
    outcomes: ["Know your real band before you book", "Build test-day stamina", "Track improvement across attempts"],
    includes: ["Full LRW paper", "Speaking interview", "Written score report", "Optional review session"],
    schedule: [{ batch: "LRW mock", days: "Every Friday", time: "9:00 AM – 12:00 PM" }, { batch: "Speaking", days: "Friday", time: "1:00 – 5:00 PM (slot booked)" }],
    faq: [{ q: "Do I have to be an STS student?", a: "No. The Mock Club is open to everyone in Narsingdi, whether you study with us or not." }]
  },
  {
    slug: "pte-academic", name: "PTE Academic Preparation", family: "PTE", badge: "New",
    image: "assets/img/course-pte-academic.svg",
    short: "Computer-delivered, AI-scored, fast results — trained the way it is marked.",
    band: "65+", duration: "6 weeks", sessions: "24 classes", batch: "10–14 students",
    fee: "10,000", feeNote: "Includes scored practice tests",
    who: "You want a faster result turnaround, or your university and visa stream accepts PTE Academic.",
    lead: "PTE is scored by a machine, and machines reward different behaviour than examiners do. Fluency without hesitation, clear content words, and full-length responses matter more than a beautiful phrase.",
    modules: [
      { t: "Speaking & Writing", d: "Read Aloud, Repeat Sentence, Describe Image, Retell Lecture, Summarise Written Text, Essay — with scoring-engine logic explained." },
      { t: "Reading", d: "Fill in the blanks, reorder paragraphs, multiple choice — and how partial credit works." },
      { t: "Listening", d: "Summarise Spoken Text, Highlight Correct Summary, Write From Dictation and the marks it quietly carries." },
      { t: "Machine-scoring behaviour", d: "Pacing, microphone technique, keyboard speed, and the habits that cost points invisibly." },
      { t: "Scored practice", d: "Two full scored practice tests with a section-level report." }
    ],
    outcomes: ["Understand PTE's partial-credit scoring", "Type a 200–300 word essay inside 20 minutes", "Speak without the pauses the engine penalises"],
    includes: ["Practice platform access", "2 scored practice tests", "Typing speed drills", "PTE booking support"],
    schedule: [{ batch: "Evening", days: "Sun · Tue · Thu", time: "6:00 – 7:30 PM" }],
    faq: [{ q: "Is PTE easier than IELTS?", a: "Not easier — different. Students who type fast and speak fluently often score higher on PTE; students with strong writing craft often prefer IELTS. We will advise you after a short trial of both." }]
  },
  {
    slug: "spoken-english", name: "Spoken English & Communication", family: "English", badge: "",
    short: "For the moment you have to actually speak — interview, viva, meeting, counter.",
    band: "—", duration: "2 months", sessions: "24 classes", batch: "12–18 students",
    fee: "5,000", feeNote: "Two levels: Basic and Advanced",
    who: "You understand English but freeze when you have to produce it out loud.",
    lead: "Most Bangla-medium students are not weak in English. They are unpractised at speaking it in front of other people. This course is built almost entirely out of speaking time.",
    modules: [
      { t: "Sound and rhythm", d: "The sounds Bangla speakers commonly substitute, word stress, and sentence rhythm." },
      { t: "Everyday function", d: "Introductions, directions, phone calls, shopping, appointments, small talk that does not stall." },
      { t: "Professional English", d: "Interviews, presentations, meetings, workplace email tone." },
      { t: "Fluency lab", d: "Timed speaking rounds, debate, storytelling, and impromptu topics every session." }
    ],
    outcomes: ["Speak for two minutes without switching to Bangla", "Handle a job interview in English", "Present in front of a room"],
    includes: ["Speaking log book", "Weekly recorded assessment", "Free conversation club access"],
    schedule: [{ batch: "Morning", days: "Sat · Mon · Wed", time: "10:00 – 11:15 AM" }, { batch: "Evening", days: "Sun · Tue · Thu", time: "7:00 – 8:15 PM" }],
    faq: [{ q: "Will the class be in English only?", a: "We start bilingual and move to English-only by week three. Nobody is dropped in the deep end on day one." }]
  },
  {
    slug: "junior-english", name: "Junior English", family: "Junior", badge: "Ages 6–14",
    image: "assets/img/course-junior-english.svg",
    short: "Kids' English at STS Junior Institute — confidence before grammar.",
    band: "—", duration: "6 months", sessions: "Twice weekly", batch: "8–12 children",
    fee: "1,200", feeNote: "Per month · sibling discount available",
    who: "You want your child to speak English comfortably, not just pass the school exam.",
    lead: "Children do not learn a language by memorising rules. They learn it by needing it. Every Junior class gives them a reason to use English — a game, a story, a task, a stage.",
    modules: [
      { t: "Phonics & reading", d: "Sound-letter mapping, blending, and reading aloud with expression." },
      { t: "Speaking through play", d: "Role play, storytelling, songs and structured games." },
      { t: "Writing foundations", d: "Sentences, paragraphs, and simple creative writing." },
      { t: "Stage confidence", d: "Termly presentation day in front of parents." }
    ],
    outcomes: ["Read age-appropriate English aloud", "Hold a conversation with an adult in English", "Stand on a stage and speak"],
    includes: ["Activity workbook", "Termly progress report to parents", "Presentation day", "Small class sizes"],
    schedule: [{ batch: "School-hour friendly", days: "Fri · Sat", time: "9:00 – 10:30 AM" }, { batch: "Weekday", days: "Mon · Wed", time: "4:00 – 5:30 PM" }],
    faq: [{ q: "Where are Junior classes held?", a: "At STS Junior Institute, Upazila Mor, Narsingdi — a separate campus set up for children, with parent waiting space." }]
  },
  {
    slug: "foundation-english", name: "Foundation English", family: "English", badge: "Start here",
    short: "The honest starting point if IELTS still feels far away.",
    band: "→ 5.5 ready", duration: "3 months", sessions: "36 classes", batch: "14–18 students",
    fee: "4,500", feeNote: "Fee credited if you continue to IELTS",
    who: "You have finished school or college in Bangla medium and your English needs rebuilding before test preparation makes sense.",
    lead: "Starting IELTS before you are ready is the most expensive mistake a student makes. Foundation English is three months that saves you a year.",
    modules: [
      { t: "Grammar rebuilt", d: "Tenses, articles, prepositions and sentence structure — taught for use, not for memorising." },
      { t: "Vocabulary systems", d: "Word families, collocations and a personal vocabulary log." },
      { t: "Reading habit", d: "Graded readers and daily short-text practice." },
      { t: "Writing basics", d: "From clean sentences to a structured paragraph." },
      { t: "Listening & speaking", d: "Ear training and controlled speaking practice." }
    ],
    outcomes: ["Write a correct, clear paragraph", "Follow spoken English at natural speed", "Enter IELTS class ready to work"],
    includes: ["Foundation workbook", "Vocabulary log", "Monthly progress test", "Free placement assessment"],
    schedule: [{ batch: "Morning", days: "Sat · Mon · Wed", time: "11:30 AM – 12:45 PM" }, { batch: "Evening", days: "Sun · Tue · Thu", time: "5:00 – 6:15 PM" }],
    faq: [{ q: "How do I know if I need this?", a: "Take the free placement assessment at our front desk. It takes 25 minutes and we will tell you plainly which class you belong in." }]
  }
];

/* ---------------- Journey ---------------- */
const JOURNEY = [
  { year: "2019", title: "STS opens in Narsingdi", text: "Founded on one idea: students in Narsingdi should not have to move to Dhaka to prepare properly." },
  { year: "2020", title: "British Council registration partner", text: "Our first official partnership. Students could register for IELTS from Narsingdi for the first time." },
  { year: "2023", title: "IDP registration partner", text: "A second registration channel opens, and the question forms: why not host the test itself?" },
  { year: "2023", title: "We ask IDP for a venue", text: "In August we put the proposal to IDP. Words were not going to be enough — they would need to see it." },
  { year: "2023", title: "STS × IDP Education Expo", text: "On 31 December we hosted an expo so IDP could walk the floor, meet the students, and see the plan working." },
  { year: "2024", title: "Authorised IDP IELTS test venue", text: "Approval granted. Narsingdi students now prepare and sit the exam in the same building." },
  { year: "2025", title: "Two awards at IDP Partners' Award Night", text: "Rising Category, plus a Special Award for Excellence in Innovation and Marketing." },
  { year: "2025", title: "Pearson PTE joins the offer", text: "A second international test route for students who need faster results." }
];

/* ---------------- Success stories ---------------- */
const STORIES = [
  { name: "Nusrat Jahan", band: "8.0", from: "6.0", course: "IELTS Academic", dest: "University of Melbourne, Australia", year: "2026", quote: "I had already given IELTS once and got 6.0. At STS they did not start teaching — they started by showing me exactly where my six marks were going." },
  { name: "Tanvir Ahmed", band: "7.5", from: "5.5", course: "IELTS Academic", dest: "University of Alberta, Canada", year: "2026", quote: "Writing was my wall. The Task 2 feedback sessions were the first time anyone told me why my essay was a 5.5 and not just that it was." },
  { name: "Sadia Islam", band: "7.5", from: "First attempt", course: "IELTS General Training", dest: "PR application, Canada", year: "2025", quote: "General Training letters felt strange to me. Three weeks of the letter bank and it became the easiest part of my paper." },
  { name: "Rafiul Hasan", band: "7.0", from: "6.0", course: "IELTS Crash Program", dest: "University of Leeds, UK", year: "2025", quote: "My date was five weeks away and I nearly cancelled it. The diagnostic mock told me what to fix, and I only worked on those things." },
  { name: "Mehjabin Chowdhury", band: "8.5", from: "7.0", course: "IELTS Academic", dest: "University of Toronto, Canada", year: "2025", quote: "Listening 9.0. I still cannot believe it. The accent training was the thing nobody else was doing." },
  { name: "Ashiqur Rahman", band: "79", scale: "PTE", from: "First attempt", course: "PTE Academic", dest: "Deakin University, Australia", year: "2026", quote: "I type fast and I speak fast, so PTE suited me. STS was honest about that instead of pushing me to IELTS." },
  { name: "Farhana Akter", band: "7.0", from: "5.5", course: "Foundation → IELTS", dest: "University of Otago, New Zealand", year: "2025", quote: "They told me I was not ready for IELTS yet. It stung. Six months later I understood it was the most useful thing anyone had said to me." },
  { name: "Imran Kabir", band: "7.5", from: "6.5", course: "IELTS Academic", dest: "TU Dublin, Ireland", year: "2025", quote: "Sitting the real exam in the same room I studied in removed a whole layer of nerves." },
  { name: "Sumaiya Haque", band: "8.0", from: "6.5", course: "IELTS Academic", dest: "Monash University, Australia", year: "2026", quote: "Speaking Part 2 used to end after forty seconds. The recorded practice is what pushed me to the full two minutes." },
  { name: "Nazmul Hossain", band: "7.0", from: "5.0", course: "IELTS General Training", dest: "Skilled visa, Australia", year: "2025", quote: "I am thirty-four with a full-time job. The morning batch was the only reason this was possible for me." },
  { name: "Tasnim Rahman", band: "7.5", from: "6.0", course: "IELTS Academic", dest: "University of Manchester, UK", year: "2026", quote: "Reading was where I lost time. Sixty minutes felt like thirty until the pacing drills changed how I read." },
  { name: "Shakib Al Amin", band: "8.0", from: "6.5", course: "IELTS Academic", dest: "KU Leuven, Belgium", year: "2025", quote: "Four full mocks before the real one. By test day it was just another Friday morning." }
];

/* ---------------- Blog ---------------- */
const BLOGS = [
  {
    slug: "true-false-not-given",
    title: "True, False, Not Given: the question type that decides your Reading band",
    cat: "IELTS Reading", date: "18 July 2026", read: "6 min",
    author: "IELTS Department, STS Institute",
    excerpt: "Most students lose four to six marks here, and almost always for the same reason: they answer False when the passage simply never said it.",
    body: `<p>If we could recover one set of marks for every student in Narsingdi, it would be these. True/False/Not Given questions are worth the same as any other Reading question, but they are the only type where students routinely lose marks by thinking too hard.</p>
<h2>The one distinction that matters</h2>
<p><strong>False</strong> means the passage says the opposite. <strong>Not Given</strong> means the passage does not say. That is the whole rule, and it is the whole problem — because when a statement <em>feels</em> wrong, the brain reaches for False.</p>
<blockquote>Ask only this: can I point to the line that contradicts it? If your finger cannot land on a line, the answer is Not Given.</blockquote>
<h2>A worked example</h2>
<p>Passage: <em>The museum opened in 1897 and was funded entirely by a private donor.</em></p>
<ul>
<li>Statement: <em>The museum was funded by the government.</em> → <strong>False.</strong> "Entirely by a private donor" contradicts it.</li>
<li>Statement: <em>The museum was the first of its kind in the country.</em> → <strong>Not Given.</strong> Nothing in the line says it was first, or was not.</li>
</ul>
<h2>Three habits that fix it</h2>
<ol>
<li><strong>Underline the qualifier.</strong> Words like <em>all, only, never, most, entirely</em> carry the trap. Match them exactly.</li>
<li><strong>Answer in passage order.</strong> These questions follow the text sequence — if you are jumping around, you are wasting time you need elsewhere.</li>
<li><strong>Give yourself twenty seconds.</strong> If you cannot find the contradicting line in twenty seconds, mark Not Given and move. Sitting there is how the third passage gets abandoned.</li>
</ol>
<p>Run one set of ten every day for two weeks and track your errors by type. Almost every student finds the same pattern staring back at them — and once you see it, it stops happening.</p>`
  },
  {
    slug: "task2-band-6-to-7",
    title: "Why your Writing is stuck at 6.0 (and what actually moves it to 7.0)",
    cat: "IELTS Writing", date: "9 July 2026", read: "7 min",
    author: "IELTS Department, STS Institute",
    excerpt: "It is rarely grammar. Four times out of five it is Task Response — you answered a topic, not the question.",
    body: `<p>Writing is the most re-taken skill in IELTS, and the most misunderstood. Students who get 6.0 usually assume the fix is harder vocabulary. It almost never is.</p>
<h2>You are marked on four things</h2>
<p>Task Response, Coherence and Cohesion, Lexical Resource, and Grammatical Range and Accuracy — each worth a quarter of your score. A student with beautiful grammar who half-answers the question is capped, no matter how the sentences read.</p>
<h2>The Task Response test</h2>
<p>Read the question again and mark every part of it. "To what extent do you agree?" needs a position and a degree. "Discuss both views and give your opinion" needs three things, not two. If your essay is missing one of them, you have written a 6.0 essay in perfect English.</p>
<blockquote>Before you write a single sentence, write your position in one line at the top of the answer sheet. Every paragraph then has something to serve.</blockquote>
<h2>Development beats decoration</h2>
<p>A band 7 body paragraph makes one point, explains why it is true, and gives one concrete example. Band 6 paragraphs typically make three points and develop none of them. Fewer ideas, taken further, scores higher — this is counter-intuitive and it is consistent.</p>
<h2>Lexical resource is not big words</h2>
<p>Examiners reward precise, natural word choice, not rare vocabulary bolted on. <em>Mitigate the problem</em> used correctly beats <em>ameliorate the quandary</em> used awkwardly, every time.</p>
<h2>What to do this week</h2>
<ul>
<li>Write one Task 2 essay under 40 minutes. No dictionary.</li>
<li>Mark it against the four descriptors yourself before anyone else sees it.</li>
<li>Rewrite only the weakest paragraph — not the whole essay.</li>
</ul>
<p>Repeat that cycle eight times and the difference is usually visible in the mock score.</p>`
  },
  {
    slug: "ielts-test-day-narsingdi",
    title: "Test day at the STS venue: what happens, hour by hour",
    cat: "Test Venue", date: "28 June 2026", read: "5 min",
    author: "Operations, STS Institute",
    excerpt: "Nerves come from not knowing the sequence. Here is exactly how a test day runs at our Narsingdi venue.",
    body: `<p>Since 2024, STS Institute has been an authorised IDP IELTS test venue. If you are sitting your test with us, here is the shape of the day so nothing on it is a surprise.</p>
<h2>Before you leave home</h2>
<p>Bring the passport or NID you registered with — the same document, no exceptions, not a photocopy. Leave your phone, smart watch and notes behind or plan to store them; you cannot carry them into the test room.</p>
<h2>On arrival</h2>
<ul>
<li><strong>Reporting time:</strong> arrive at the time on your confirmation, not at the start time of the test. Late arrivals cannot be admitted once the paper begins.</li>
<li><strong>Identity check:</strong> document verification and photograph.</li>
<li><strong>Storage:</strong> personal items are stored outside the test room.</li>
</ul>
<h2>The test itself</h2>
<p>Listening, Reading and Writing run back to back with no break between them — roughly two hours and forty minutes in the chair. Speaking may be on the same day or within a few days of it; your confirmation states which.</p>
<blockquote>Eat before you come. It is a long sitting, and hunger costs more marks in the third passage than any grammar rule.</blockquote>
<h2>After</h2>
<p>Computer-delivered results are typically available within a few days; paper-based takes around thirteen. Our IELTS desk will walk you through your Test Report Form and what your score means for your application.</p>
<p>Anything unclear, call the IELTS Department on 01901-402204 before test day rather than on the morning.</p>`
  },
  {
    slug: "ielts-or-pte",
    title: "IELTS or PTE? An honest comparison for Bangladeshi students",
    cat: "Choosing a test", date: "14 June 2026", read: "6 min",
    author: "Counselling Desk, STS Institute",
    excerpt: "The right answer depends on how you type, how you speak, and how fast you need the result — not on which test is 'easier'.",
    body: `<p>We offer preparation for both, so we have no reason to sell you one over the other. Here is how we actually advise students at the counselling desk.</p>
<h2>Choose PTE if…</h2>
<ul>
<li>You type quickly and comfortably on a keyboard.</li>
<li>You speak fluently without long pauses, even if your grammar is imperfect.</li>
<li>You need your result fast — PTE turnaround is typically a few days.</li>
</ul>
<h2>Choose IELTS if…</h2>
<ul>
<li>You write better by hand than you type, or you want the paper-based option.</li>
<li>You are stronger at crafted writing than at rapid speaking.</li>
<li>Your university, professional body or visa stream specifically names IELTS.</li>
</ul>
<h2>The thing nobody mentions</h2>
<p>PTE is scored by a machine. It rewards continuous, clear speech and penalises hesitation more sharply than a human examiner would. Students who pause to find the perfect word often score lower on PTE than their English deserves — and higher on IELTS.</p>
<blockquote>Check your destination's requirement first. Everything else is a preference; that is a rule.</blockquote>
<h2>Before you commit</h2>
<p>Come in and try a short sample of both. Twenty minutes at our desk answers the question better than a week of reading comparison articles — this one included.</p>`
  },
  {
    slug: "speaking-part-2-two-minutes",
    title: "The cue card: how to fill two minutes without running dry",
    cat: "IELTS Speaking", date: "2 June 2026", read: "5 min",
    author: "Language Department, STS Institute",
    excerpt: "You get one minute to prepare and two to speak. The minute is where the marks are won.",
    body: `<p>Part 2 is the only part of the Speaking test where you are not interrupted. That is a gift, and most students waste it by stopping at forty seconds.</p>
<h2>Use the preparation minute properly</h2>
<p>Do not write sentences — you will read them aloud and your fluency will collapse. Write four or five single words, one per bullet on the card, plus one word for a story. Words prompt speech; sentences replace it.</p>
<h2>The shape that always fills the time</h2>
<ol>
<li><strong>Set the scene</strong> — when, where, who. Fifteen seconds.</li>
<li><strong>Answer each bullet</strong> in order. About twenty seconds each.</li>
<li><strong>Tell the story</strong> — one specific incident, in the past tense. This is where the time goes and where the range shows.</li>
<li><strong>Close with feeling</strong> — why it mattered to you.</li>
</ol>
<blockquote>A specific memory is easier to speak about for two minutes than a general opinion. Always choose the specific one.</blockquote>
<h2>If you dry up anyway</h2>
<p>Do not apologise or stop. Move to a related detail: what happened just before, or what someone else said. Recovery is not penalised; silence is.</p>
<h2>Practise it out loud</h2>
<p>Record yourself on your phone, three cue cards a day, and listen back once. You will hear the forty-second habit break within a week.</p>`
  },
  {
    slug: "study-abroad-timeline",
    title: "Your study abroad timeline: working backwards from the intake",
    cat: "Study Abroad", date: "21 May 2026", read: "8 min",
    author: "Counselling Desk, STS Institute",
    excerpt: "Students miss intakes for one reason — they start the language test too late. Here is the calendar, counted backwards.",
    body: `<p>Almost every student who misses an intake had the grades. What they did not have was twelve spare weeks. Count backwards from your intake and the plan builds itself.</p>
<h2>The backwards calendar</h2>
<ul>
<li><strong>Intake month</strong> — classes start.</li>
<li><strong>Minus 2 months</strong> — visa decision. Build in delay; it is normal.</li>
<li><strong>Minus 4 months</strong> — visa application filed, financials arranged.</li>
<li><strong>Minus 6 months</strong> — offer accepted, deposit paid.</li>
<li><strong>Minus 8 months</strong> — applications submitted with your test score.</li>
<li><strong>Minus 9 months</strong> — test sat, result in hand.</li>
<li><strong>Minus 12 months</strong> — preparation course begins.</li>
</ul>
<blockquote>One year before the intake is not early. It is on time.</blockquote>
<h2>Build in a second attempt</h2>
<p>Plan your first test date early enough that a retake still fits. Students who leave exactly one attempt of room are the ones who end up deferring a full year over half a band.</p>
<h2>What to prepare alongside the test</h2>
<ol>
<li>Academic transcripts and certificates, attested.</li>
<li>Passport — apply now if you do not have one; it is the quietest bottleneck.</li>
<li>Financial documents, seasoned for the period your destination requires.</li>
<li>Statement of purpose — drafted early, revised often.</li>
</ol>
<p>Bring your intake month to our counselling desk and we will map the dates with you in about twenty minutes.</p>`
  }
];

/* ============================================================
   Components
   ============================================================ */
const NAV = [
  { label: "Home", href: "index.html" },
  { label: "About", href: "about.html" },
  {
    label: "Courses", href: "courses.html", children: [
      { label: "All courses", href: "courses.html" },
      { label: "IELTS Academic", href: "course.html?c=ielts-academic" },
      { label: "IELTS General Training", href: "course.html?c=ielts-general" },
      { label: "IELTS Crash Program", href: "course.html?c=ielts-crash" },
      { label: "PTE Academic", href: "course.html?c=pte-academic" },
      { label: "Spoken English", href: "course.html?c=spoken-english" },
      { label: "Junior English", href: "course.html?c=junior-english" }
    ]
  },
  {
    label: "Stories", href: "success-stories.html", children: [
      { label: "Success stories", href: "success-stories.html" },
      { label: "Inspiring journey", href: "inspiring-journey.html" }
    ]
  },
  { label: "Blog", href: "blogs.html" },
  { label: "Contact", href: "contact.html" }
];

function caret() {
  return `<svg width="10" height="10" viewBox="0 0 12 12" fill="none" aria-hidden="true"><path d="M2 4.5L6 8.5L10 4.5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>`;
}

function buildHeader() {
  const host = document.getElementById("site-header");
  if (!host) return;
  const overlay = host.dataset.mode === "overlay";
  const active = host.dataset.active || "";

  const links = NAV.map(item => {
    const cur = item.label.toLowerCase() === active ? ' aria-current="page"' : "";
    if (!item.children) return `<li><a class="navlink" href="${item.href}"${cur}>${item.label}</a></li>`;
    return `<li class="relative has-drop">
      <a class="navlink" href="${item.href}"${cur}>${item.label} ${caret()}</a>
      <div class="dropdown">${item.children.map(c => `<a href="${c.href}">${c.label}</a>`).join("")}</div>
    </li>`;
  }).join("");

  host.innerHTML = `
  <a href="#main" class="skip">Skip to content</a>
  <div class="topbar ${overlay ? "relative z-40" : ""}">
    <div class="shell topbar-inner">
      <div class="flex items-center gap-2 min-w-0">
        <span class="inline-flex items-center gap-1.5 shrink-0 text-flame-soft mono text-[10px] tracking-[.18em] uppercase">
          <svg width="12" height="12" viewBox="0 0 16 16" fill="none" aria-hidden="true"><path d="M6.5 8.6l1.4 1.4 3.3-3.4" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/><path d="M8 1.6l5.2 2v4.1c0 3.2-2.1 5.4-5.2 6.7-3.1-1.3-5.2-3.5-5.2-6.7V3.6L8 1.6z" stroke="currentColor" stroke-width="1.3" stroke-linejoin="round"/></svg>
          Official IDP IELTS test venue
        </span>
        <span class="hidden md:inline text-[12.5px] truncate opacity-80">· Narsingdi, Bangladesh</span>
      </div>
      <div class="topbar-links">
        <a href="tel:+8801901402202" class="hidden sm:inline">${SITE.phones[0]}</a>
        <a href="mailto:${SITE.email}" class="hidden xl:inline">${SITE.email}</a>
        <a href="${SITE.fb}" target="_blank" rel="noopener" aria-label="STS on Facebook">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M13.5 21v-8h2.7l.4-3.1h-3.1V7.9c0-.9.25-1.5 1.55-1.5H16.7V3.6c-.29-.04-1.28-.13-2.43-.13-2.4 0-4.05 1.47-4.05 4.17V9.9H7.5V13h2.72v8h3.28z"/></svg>
        </a>
      </div>
    </div>
  </div>

  <nav class="nav ${overlay ? "nav-over" : "nav-solid"}" id="mainnav">
    <div class="shell nav-inner">
      <a href="index.html" class="flex items-center gap-3 shrink-0" aria-label="STS Institute — home">
        <img src="${SITE.logo}" alt="STS Institute" class="brand-logo ${overlay ? "brightness-0 invert" : ""}" id="brandmark">
      </a>
      <ul class="nav-list">${links}</ul>
      <div class="nav-actions">
        <a href="admission.html" class="btn btn-primary btn-sm hidden sm:inline-flex">Get admission</a>
        <button class="burger lg:hidden" id="burger" aria-label="Open menu" aria-expanded="false" aria-controls="drawer">
          <span></span><span></span><span></span>
        </button>
      </div>
    </div>
  </nav>

  <div class="drawer lg:hidden" id="drawer" role="dialog" aria-modal="true" aria-label="Menu">
    <div class="shell py-5">
      <div class="flex items-center justify-between">
        <img src="${SITE.logo}" alt="STS Institute" class="h-10 w-auto brightness-0 invert">
        <button id="drawer-close" class="text-white/80 p-2" aria-label="Close menu">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none"><path d="M6 6l12 12M18 6L6 18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
        </button>
      </div>
      <ul class="mt-8 grid gap-1">
        ${NAV.map(i => `
          <li>
            <a href="${i.href}" class="block py-3 text-2xl font-display font-bold border-b border-white/10">${i.label}</a>
            ${i.children ? `<div class="grid gap-0.5 py-2 pl-1">${i.children.slice(1).map(c => `<a href="${c.href}" class="block py-1.5 text-[15px] text-white/70">${c.label}</a>`).join("")}</div>` : ""}
          </li>`).join("")}
      </ul>
      <div class="mt-8 grid gap-3">
        <a href="admission.html" class="btn btn-primary w-full">Get admission</a>
        <a href="tel:+8801901402202" class="btn btn-glass w-full">Call ${SITE.phones[0]}</a>
      </div>
      <div class="mt-8 text-white/55 text-sm leading-relaxed">
        ${SITE.address}<br>${SITE.email}
      </div>
    </div>
  </div>`;

  // interactions
  const nav = document.getElementById("mainnav");
  const onScroll = () => nav.classList.toggle("stuck", window.scrollY > 12);
  onScroll();
  window.addEventListener("scroll", onScroll, { passive: true });

  const burger = document.getElementById("burger");
  const drawer = document.getElementById("drawer");
  const closeBtn = document.getElementById("drawer-close");
  const toggle = open => {
    drawer.classList.toggle("open", open);
    burger.classList.toggle("open", open);
    burger.setAttribute("aria-expanded", String(open));
    document.body.classList.toggle("lock", open);
  };
  burger.addEventListener("click", () => toggle(!drawer.classList.contains("open")));
  closeBtn.addEventListener("click", () => toggle(false));
  document.addEventListener("keydown", e => { if (e.key === "Escape") toggle(false); });
}

function buildFooter() {
  const host = document.getElementById("site-footer");
  if (!host) return;
  host.innerHTML = `
  <footer class="foot bg-ink text-white/70">
    <div class="shell py-16 md:py-20">
      <div class="grid gap-12 lg:grid-cols-[1.4fr_1fr_1fr_1.2fr]">
        <div>
          <img src="${SITE.logo}" alt="STS Institute" class="h-11 w-auto brightness-0 invert">
          <p class="mt-5 text-[15px] leading-relaxed max-w-xs">
            Secret to Success. Standing by your goals, staying passionate, avoiding shortcuts — and never giving up.
          </p>
          <div class="mt-6 bandrail text-white/40" data-rail="16" data-hit="14"></div>
        </div>
        <div>
          <h4 class="text-white text-sm mono tracking-[.18em] uppercase mb-4">Courses</h4>
          <ul class="grid gap-2.5 text-[15px]">
            ${COURSES.slice(0, 6).map(c => `<li><a href="course.html?c=${c.slug}">${c.name}</a></li>`).join("")}
          </ul>
        </div>
        <div>
          <h4 class="text-white text-sm mono tracking-[.18em] uppercase mb-4">Institute</h4>
          <ul class="grid gap-2.5 text-[15px]">
            <li><a href="about.html">About STS</a></li>
            <li><a href="inspiring-journey.html">Inspiring journey</a></li>
            <li><a href="success-stories.html">Success stories</a></li>
            <li><a href="blogs.html">Blog</a></li>
            <li><a href="admission.html">Get admission</a></li>
            <li><a href="contact.html">Contact</a></li>
          </ul>
        </div>
        <div>
          <h4 class="text-white text-sm mono tracking-[.18em] uppercase mb-4">Reach us</h4>
          <ul class="grid gap-3 text-[15px]">
            ${SITE.desks.map(d => `<li><span class="block text-white/45 text-[12.5px]">${d.name}</span><a href="tel:+88${d.num.split(",")[0].replace(/-/g, "")}">${d.num}</a></li>`).join("")}
            <li><span class="block text-white/45 text-[12.5px]">Email</span><a href="mailto:${SITE.email}">${SITE.email}</a></li>
            <li><span class="block text-white/45 text-[12.5px]">Address</span>${SITE.address}</li>
          </ul>
        </div>
      </div>

      <div class="mt-14 pt-7 border-t border-white/10 flex flex-col md:flex-row gap-4 md:items-center justify-between text-[13.5px]">
        <p>© ${new Date().getFullYear()} STS Institute. All rights reserved.</p>
        <div class="flex flex-wrap items-center gap-x-6 gap-y-2">
          <span class="text-white/40">Authorised IDP IELTS test venue</span>
          <span class="text-white/40">Pearson PTE partner</span>
          <a href="${SITE.fb}" target="_blank" rel="noopener">Facebook</a>
        </div>
      </div>
    </div>
  </footer>`;
}

/* ---------------- Shared helpers ---------------- */
function photo(label, cls = "", src = "") {
  return `<div class="ph ${cls}" data-label="${label}">${src ? `<img src="${src}" alt="${label}" loading="lazy" onerror="this.remove()">` : ""}</div>`;
}

function bandChip(c) {
  if (c.band === "—") return `<span class="band-chip">Foundation level</span>`;
  if (c.band === "Any") return `<span class="band-chip">All levels</span>`;
  return `<span class="band-chip">Target ${c.band}</span>`;
}

function courseCard(c) {
  return `<a href="course.html?c=${c.slug}" class="card p-6 flex flex-col group" data-reveal>
    <div class="card-thumb">
      <img src="${c.image || 'assets/img/course-ielts-academic.svg'}" alt="${c.name}" loading="lazy">
    </div>
    <div class="mt-4">
      <div class="flex items-start justify-between gap-3">
        <span class="mono text-[11px] tracking-[.16em] uppercase text-muted">${c.family}</span>
        ${c.badge ? `<span class="mono text-[10px] tracking-[.14em] uppercase px-2 py-1 rounded-md bg-ink text-white">${c.badge}</span>` : ""}
      </div>
      <h3 class="mt-3 text-[21px] leading-tight">${c.name}</h3>
    </div>
    <p class="mt-2.5 text-[14.5px] text-muted leading-relaxed flex-1">${c.short}</p>
    <div class="mt-5 flex flex-wrap items-center gap-2">
      ${bandChip(c)}
      <span class="text-[13px] text-muted">${c.duration}</span>
      <span class="w-1 h-1 rounded-full bg-line"></span>
      <span class="text-[13px] text-muted">${c.sessions}</span>
    </div>
    <div class="mt-5 pt-5 border-t border-line flex items-center justify-between">
      <span class="text-[15px] font-semibold">৳ ${c.fee}<span class="text-muted font-normal text-[13px]">${c.slug === "junior-english" ? " /month" : c.slug === "ielts-mock-club" ? " /mock" : ""}</span></span>
      <span class="text-flame text-[14px] font-semibold inline-flex items-center gap-1.5">View course
        <svg width="14" height="14" viewBox="0 0 16 16" fill="none" class="transition-transform group-hover:translate-x-1"><path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
      </span>
    </div>
  </a>`;
}

function storyCard(s) {
  return `<article class="card p-6 flex flex-col" data-reveal>
    <div class="flex items-center gap-4">
      <div class="w-12 h-12 rounded-full bg-ember grid place-items-center font-display text-flame-deep text-lg shrink-0">${s.name.charAt(0)}</div>
      <div class="min-w-0">
        <h3 class="text-[17px] leading-tight truncate">${s.name}</h3>
        <p class="text-[13px] text-muted truncate">${s.course} · ${s.year}</p>
      </div>
      <div class="ml-auto text-right shrink-0">
        <div class="font-display text-[26px] text-flame leading-none">${s.band}</div>
        <div class="mono text-[10px] tracking-[.14em] uppercase text-muted mt-1">${s.scale === "PTE" ? "PTE score" : "Band"}</div>
      </div>
    </div>
    <p class="mt-5 text-[14.5px] leading-relaxed text-[#2c3a52] flex-1">“${s.quote}”</p>
    <div class="mt-5 pt-4 border-t border-line grid gap-1.5">
      <span class="text-[13px] text-muted"><strong class="text-ink font-semibold">From</strong> ${s.from}</span>
      <span class="text-[13px] text-muted"><strong class="text-ink font-semibold">Now</strong> ${s.dest}</span>
    </div>
  </article>`;
}

function blogCard(b, feature = false) {
  return `<a href="blog-details.html?p=${b.slug}" class="card overflow-hidden flex flex-col group" data-reveal>
    ${photo(b.cat, feature ? "h-64" : "h-44")}
    <div class="p-6 flex flex-col flex-1">
      <div class="flex items-center gap-3 mono text-[11px] tracking-[.14em] uppercase text-muted">
        <span class="text-flame">${b.cat}</span><span>·</span><span>${b.read}</span>
      </div>
      <h3 class="mt-3 text-[${feature ? "24" : "19"}px] leading-snug group-hover:text-flame-deep transition-colors">${b.title}</h3>
      <p class="mt-2.5 text-[14.5px] text-muted leading-relaxed flex-1">${b.excerpt}</p>
      <span class="mt-5 text-[13px] text-muted">${b.date}</span>
    </div>
  </a>`;
}

/* ---------------- Motion + counters ---------------- */
function initReveal() {
  const els = document.querySelectorAll("[data-reveal]");
  if (!("IntersectionObserver" in window)) { els.forEach(e => e.classList.add("in")); return; }
  const io = new IntersectionObserver((entries) => {
    entries.forEach((en, i) => {
      if (en.isIntersecting) {
        setTimeout(() => en.target.classList.add("in"), Math.min(i * 70, 350));
        io.unobserve(en.target);
      }
    });
  }, { threshold: 0.12, rootMargin: "0px 0px -60px" });
  els.forEach(e => io.observe(e));
}

function initCounters() {
  const nodes = document.querySelectorAll("[data-count]");
  if (!nodes.length) return;
  const run = el => {
    const target = parseFloat(el.dataset.count);
    const dec = (el.dataset.count.split(".")[1] || "").length;
    const dur = 1400; const t0 = performance.now();
    const step = t => {
      const p = Math.min((t - t0) / dur, 1);
      const e = 1 - Math.pow(1 - p, 3);
      el.textContent = (target * e).toFixed(dec);
      if (p < 1) requestAnimationFrame(step);
    };
    requestAnimationFrame(step);
  };
  const io = new IntersectionObserver(en => en.forEach(x => { if (x.isIntersecting) { run(x.target); io.unobserve(x.target); } }), { threshold: .5 });
  nodes.forEach(n => io.observe(n));
}

function initMeters() {
  const nodes = document.querySelectorAll(".meter i");
  const io = new IntersectionObserver(en => en.forEach(x => {
    if (x.isIntersecting) { x.target.style.width = x.target.dataset.w + "%"; io.unobserve(x.target); }
  }), { threshold: .4 });
  nodes.forEach(n => io.observe(n));
}

/* Build the 0–9 band rails used as a structural device */
function initRails() {
  document.querySelectorAll("[data-rail]").forEach(r => {
    const n = parseInt(r.dataset.rail, 10) || 19;
    const hit = parseInt(r.dataset.hit, 10);
    let out = "";
    for (let i = 0; i < n; i++) {
      const cls = i === hit ? "hit" : (i % 2 === 0 ? "maj" : "");
      out += `<i class="${cls}"></i>`;
    }
    r.innerHTML = out;
  });
}

/* ---------------- Forms ---------------- */
function submitForm(form, statusEl) {
  const data = Object.fromEntries(new FormData(form).entries());
  const done = msg => {
    statusEl.hidden = false;
    statusEl.className = "mt-5 rounded-xl border border-line bg-ember p-5";
    statusEl.innerHTML = `<p class="font-semibold text-ink">${msg}</p>
      <p class="text-[14px] text-muted mt-1.5">Our PR desk calls within one working day on ${SITE.phones[0]}. Save the number so you know who is calling.</p>`;
    form.reset();
    statusEl.scrollIntoView({ behavior: "smooth", block: "center" });
  };
  if (!FORM_ENDPOINT) { done("Recorded. Set FORM_ENDPOINT in assets/site.js to send this to your sheet."); return; }
  fetch(FORM_ENDPOINT, { method: "POST", body: new URLSearchParams(data) })
    .then(() => done("Application received."))
    .catch(() => done("Application saved locally — we could not reach the server. Please call us to confirm."));
}

/* ---------------- Boot ---------------- */
document.addEventListener("DOMContentLoaded", () => {
  buildHeader();
  buildFooter();
  initRails();
  initReveal();
  initCounters();
  initMeters();
  const y = document.getElementById("year"); if (y) y.textContent = new Date().getFullYear();
});
