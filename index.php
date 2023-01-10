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

shuffle($command_names);

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

?>

<!DOCTYPE html>
<html lang="en">
<style>
table, th, td {
  border:1px solid black;
}
</style>
<body>
    <h3>ТУРЫ ПЕРВОГО КРУГА</h3>
    <table>
        <?php
        foreach ($first_round_tours as $index_outer => $tour) {
            echo '<h4>ТУР НОМЕР ' . ($index_outer + 1) . '</h4>';

            echo '<table>';
            ?>
            
            <tr>
                <th>Хозяева</th>
                <th>Гости</th>
            </tr>

            <?php
            foreach ($tour->get_matches() as $index_inner => $match) {
                echo '<tr>';

                echo '<td>';
                echo $match->get_host();
                echo '</td>';

                echo '<td>';
                echo $match->get_guest();
                echo '</td>';

                echo '</tr>';
            }
            echo '</table>';
        }
        ?>

</body>
</html>
<!--
echo 'ТУРЫ ПЕРВОГО КРУГА' . PHP_EOL;
foreach ($first_round_tours as $index_outer => $tour) {
    echo ('ТУР ' . ($index_outer + 1)) . PHP_EOL;
    foreach ($tour->get_matches() as $index_inner => $match) {
        echo ($index_inner + 1) . ' -> ' . $match . PHP_EOL;
    }
}

echo PHP_EOL . PHP_EOL;

echo 'ТУРЫ ВТОРОГО КРУГА' . PHP_EOL;
foreach ($second_round_tours as $index_outer => $tour) {
    echo ('ТУР ' . ($index_outer + 1)) . PHP_EOL;
    foreach ($tour->get_matches() as $index_inner => $match) {
        echo ($index_inner + 1) . ' -> ' . $match . PHP_EOL;
    }
}

echo PHP_EOL . count($host_playing_tours) . ' -- ' . count($guest_playing_tours) . PHP_EOL;
