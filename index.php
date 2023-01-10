<?php

$command_names = [
    'Ливерпуль',
    'Челси',
    'Тоттенхэм Хотспур',
    'Арсенал',
    'Манчестер Юнайтед',
    'Эвертон',
    'Лестер Сити',
    'Вест Хэм Юнайтед',
    'Уотфорд',
    'Борнмут',
    'Бернли',
    'Саутгемптон',
    'Брайтон энд Хоув Альбион',
    'Норвич Сити',
    'Шеффилд Юнайтед',
    'Фулхэм',
    'Сток Сити',
    'Мидлсбро',
    'Суонси Сити',
    'Дерби Каунти',
];

class FootballMatch {
    private string $host;
    private string $guest;

    public function __construct(string $host, string $guest) {
        $this->host = $host;
        $this->guest = $guest;
    }
    
    public function get_host(): string {
        return $this->host;
    }
    public function get_guest(): string {
        return $this->guest;
    }

    public function __toString(): string {
        return '[host: ' . $this->host . ', guest: ' . $this->guest . ']';
    }
}

function generate_matches(): array {
    $matches = [];
    for ($i = 0; $i < count($GLOBALS['command_names']); $i++) {
        $host_name = $GLOBALS['command_names'][$i];
        for ($j = $i + 1; $j < count($GLOBALS['command_names']); $j++) {
            $guest_name = $GLOBALS['command_names'][$j];

            $match = new FootballMatch(host: $host_name, guest: $guest_name);
            $matches[] = $match;
        }
    }
    return $matches;
}

function swap_guest_host(FootballMatch $match): FootballMatch {
    return new FootballMatch(host: $match->get_guest(), guest: $match->get_host());
}

$unique_matches = generate_matches();

$first_round_matches = [];
$second_round_matches = [];

foreach ($unique_matches as $index => $match) {
    $first_round_matches[] = $match;
    $second_round_matches[] = swap_guest_host($match);
}

//$output = [];
//for ($i = 0; $i < count($first_round_matches); $i++) {
//    echo ($i + 1) . ': ' . '1st round ' . $first_round_matches[$i] . ', ' . '2nd round ' . $second_round_matches[$i] . PHP_EOL;
//}

class Tour {
    private array $matches;
    private int $max_match_count;

    public function __construct($max_match_count) {
        $this->max_match_count = $max_match_count;
        $this->matches = [];
    }

    public function add_match($match): void {
        if ($this->is_full()) {
            throw new Exception('Stack overflow');
        }
        $this->matches[] = $match;
    }
    public function get_matches(): array {
        return $this->matches;
    }

    public function is_full(): bool {
        return count($this->matches) == $this->max_match_count;
    }

    public function match_count(): int {
        return count($this->matches);
    }
}

$max_tour_match_count = 10;
$one_round_tour_count = 19;
$host_playing_tours = [];
$guest_playing_tours = [];
for ($i = 0; $i < $one_round_tour_count; $i++) {
    $host_playing_tours[] = new Tour(max_match_count: $max_tour_match_count);
    $guest_playing_tours[] = new Tour(max_match_count: $max_tour_match_count);
}

for ($i = 0; $i < count($first_round_matches); $i++) {
    $host_playing_tours[$i % $one_round_tour_count]->add_match($first_round_matches[$i]);
}
for ($i = 0; $i < count($second_round_matches); $i++) {
    $guest_playing_tours[$i % $one_round_tour_count]->add_match($second_round_matches[$i]);
}

$tours = [];
for ($i = 0; $i < $one_round_tour_count; $i++) {
    $tours[] = $host_playing_tours[$i];
    $tours[] = $guest_playing_tours[count($guest_playing_tours) - 1 - $i];
}

$first_round_tours = array_slice(array: $tours, offset: 0, length: $one_round_tour_count);
$second_round_tours = array_slice(array: $tours, offset: $one_round_tour_count, length: $one_round_tour_count);

echo 'ТУРЫ ПЕРВОГО КРУГА' . PHP_EOL;
foreach ($first_round_tours as $index => $tour) {
    echo ('ТУР ' . ($index + 1)) . PHP_EOL;
    foreach ($tour->get_matches() as $index => $match) {
        echo ($index + 1) . ' -> ' . $match . PHP_EOL;
    }
}

echo PHP_EOL . PHP_EOL;

echo 'ТУРЫ ВТОРОГО КРУГА' . PHP_EOL;
foreach ($second_round_tours as $index => $tour) {
    echo ('ТУР ' . ($index + 1)) . PHP_EOL;
    foreach ($tour->get_matches() as $index => $match) {
        echo ($index + 1) . ' -> ' . $match . PHP_EOL;
    }
}

echo PHP_EOL . count($host_playing_tours) . ' -- ' . count($guest_playing_tours) . PHP_EOL;



/*

$one_round_tour_count = 19;
$tour_max_match_count = 10;

$first_round_tours = [];
$second_round_tours = [];

for ($i = 0; $i < $one_round_tour_count; $i++) {
    $first_round_tour = new Tour(max_match_count: $tour_max_match_count);
    while (!$first_round_tour->is_full()) {
        $match = array_shift($first_round_matches);
        $first_round_tour->add_match($match);
    }
    $first_round_tours[] = $first_round_tour;

    $second_round_tour = new Tour(max_match_count: $tour_max_match_count);
    while (!$second_round_tour->is_full()) {
        $match = array_shift($second_round_matches);
        $second_round_tour->add_match($match);
    }
    $second_round_tours[] = $second_round_tour;
}

echo 'МАТЧИ ПЕРВОГО ТУРА' . PHP_EOL;
foreach ($first_round_tours as $index => $tour) {
    echo ('ТУР ' . ($index + 1)) . PHP_EOL;
    foreach ($tour->get_matches() as $index => $match) {
        echo ($index + 1) . ' -> ' . $match . PHP_EOL;
    }
}

echo 'МАТЧИ ВТОРОГО ТУРА' . PHP_EOL;
foreach ($second_round_tours as $index => $tour) {
    echo ('ТУР ' . ($index + 1)) . PHP_EOL;
    foreach ($tour->get_matches() as $index => $match) {
        echo ($index + 1) . ' -> ' . $match . PHP_EOL;
    }
}

echo PHP_EOL . count($first_round_tours) . ' -- ' . count($second_round_tours) . PHP_EOL;


/*
foreach ($first_round_matches as $index => $match) {
    for
}
for ($i = 0; $i < count($first_round_matches); $i++) {

}

/*
for ($i = 0; $i < 190; $i++) {
    echo ($i + 1) . ' 1st round -> ' . $first_round_matches[$i] . ', 2nd round -> ' . $second_round_matches[$i] . PHP_EOL;
}

echo PHP_EOL;
echo PHP_EOL;
echo count($first_round_matches) . ' <-> ' . count($second_round_matches) . PHP_EOL;


class Command {
    private $name;
    private $competitors;

    function __construct($name) {
        $this->name = $name;
        $this->competitors = [];
    }

    function add_competitor($competitor) {
        $this->competitors[] = $competitor;
    }

    function get_name() {
        return $this->name;
    }
    function get_competitors() {
        return $this-> competitors;
    }

    function get_matches() {
        $matches = [];
        foreach ($this->competitors as $index => $competitor) {
            $match = [
                'host' => $this->name
            ]
            $matches[] = 
        }
    }

    function __toString() {
        $result = 'name: ' . $this->name . PHP_EOL;
        foreach ($this->competitors as $index => $competitor) {
            $result .= '--' . ($index + 1) . ' -> ' . $competitor . PHP_EOL;
        }
        return $result;
    }
}



$commands = [];
foreach ($command_names as $name) {
    $commands[] = new Command(name: $name);
}

foreach ($commands as $command) {
    foreach ($command_names as $name) {
        if ($command->get_name() == $name) {
            continue;
        }
        $command->add_competitor($name);
    }
}

foreach ($commands as $index => $command) {
    echo ($index + 1) . ' -> ' . $command;
}


*/

/*

$command_pairs = array();
foreach ($commands as $index_outer => $command_outer) {
    foreach ($commands as $index_inner => $command_inner) {
        if ($index_outer == $index_inner) {
            continue;
        }
        $pair = [$command_outer, $command_inner];
        $command_pairs[] = $pair;
    }
}

foreach ($command_pairs as $index => $pair) {
    echo ($index + 1) . ' -> ' . $pair[0] . ' -- ' . $pair[1] . PHP_EOL;
}

$match_count = count($commands) - 1;
echo $match_count;
*/
