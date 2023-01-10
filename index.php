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

$host_matches = [];
$guest_matches = [];

$all_unique_matches = [];

foreach ($unique_matches as $index => $match) {
    $host_matches[] = $match;
    $guest_matches[] = swap_guest_host($match);
    
    $all_unique_matches[] = clone $match;
    $all_unique_matches[] = clone swap_guest_host($match);
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


function get_opposite_match_from($match, $opposite_match_array) {
    foreach ($opposite_match_array as $opposite_match) {
        if ($opposite_match->get_guest() == $match->get_host() && 
            $opposite_match->get_host() == $match->get_guest()) {
                return $opposite_match;
        }
    }
    throw new Exception('Not found');
}

function delete_match_from($match, &$match_array) {
    foreach ($match_array as $index => $m) {
        if ($m->get_host() == $match->get_host() && 
            $m->get_guest() == $match->get_guest()) {
                unset($match_array[$index]);
                return;
        }
    }
    throw new Exception('Not found');
} 

function get_match_with_guest_from($guest, $match_array) {
    foreach ($match_array as $match) {
        if ($match->get_guest() == $guest) {
            return $match;
        }
    }
    throw new Exception('Not found');
}

$m = [];
$i = 0;
while (count($host_matches) != 0) {
    try {
        $match = clone array_shift($host_matches);
        $opposite_match = clone get_opposite_match_from(match: $match, opposite_match_array: $guest_matches);
        delete_match_from(match: $opposite_match, match_array: $guest_matches);

        $next_match = clone get_match_with_guest_from(guest: $match->get_host(), match_array: $guest_matches);
        delete_match_from(match: $next_match, match_array: $guest_matches);
        $opposite_match = clone get_opposite_match_from(match: $next_match, opposite_match_array: $host_matches);
        delete_match_from(match: $opposite_match, match_array: $host_matches);
        
        $host_playing_tours[$i % $one_round_tour_count]->add_match(match: $match);
        $i++;
        $host_playing_tours[$i % $one_round_tour_count]->add_match(match: $next_match);
        $i++;

        $m[] = $match;
        $m[] = $next_match;
        //echo $match . ' --------- ' . $next_match . PHP_EOL;
        //echo count($host_matches) . ' --- ' . count($guest_matches) . PHP_EOL;
    }
    catch (Exception $ex) {
        continue;
    }
}

foreach ($host_playing_tours as $index => $tour) {
    echo 'TOUR ' . ($index + 1) . PHP_EOL;
    foreach ($tour->get_matches() as $index => $match) {
        echo ($index + 1) . ' -> ' . $match . PHP_EOL;
    }
}


$first_round_tours = $host_playing_tours;
$second_round_tours = $host_playing_tours;

?>

<!DOCTYPE html>
<html lang="en">
<style>

table, th, td {
  border:1px solid black;
}

* {
    box-sizing: border-box;
}


.column {
    float: left;
    width: 50%;
}


.row:after {
    content: "";
    display: table;
    clear: both;
}

h4 {
    margin: 0px;
}
h3 {
    margin-left: 30px;
}
table {
    margin-bottom: 25px;
}

.selected {
    background-color: #FFD700;
}

.my-table {
    margin: 20px;
}

</style>
<head>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js" type="text/javascript"></script>

<script type="text/javascript">
$("td").live('click', function() {
    var class_name = $(this).attr('class');
    $("td").removeClass('selected')
    $("td").filter('.' + class_name).toggleClass('selected');
    console.log(class_name);

    // $(this).toggleClass('selected');
});
</script>

</head>
<body>
    <div class="row">
        <div class="column" style="background-color:#90EE90;">
            <h3>ТУРЫ ПЕРВОГО КРУГА</h3>
            <?php
            foreach ($first_round_tours as $index_outer => $tour) {
                ?>
                <div class="my-table">
                <?php
                echo '<h4>ТУР НОМЕР ' . ($index_outer + 1) . '</h4>';

                echo '<table>';
                ?>
                
                <tr style="background-color:beige;">
                    <th>Хозяева</th>
                    <th>Гости</th>
                </tr>

                <?php
                foreach ($tour->get_matches() as $index_inner => $match) {
                    ?>
                    <?php $host_class_name = str_replace(' ', '', $match->get_host()); ?>
                    <?php $guest_class_name = str_replace(' ', '', $match->get_guest()); ?>

                    <tr class="<?= $host_class_name; ?> <?= $guest_class_name; ?>">
                        <td class="<?= $host_class_name; ?>">
                            <?= $match->get_host(); ?>
                        </td>
                        <td class="<?= $guest_class_name; ?>" >
                            <?= $match->get_guest(); ?>
                        </td>
                    </tr>
                    <?php
                }
                echo '</table>';
                ?>

                </div>
                <?php
            }
            ?>
        </div>
        
        <div class="column" style="background-color:#ff9999;">
            <h3>ТУРЫ ВТОРОГО КРУГА</h3>
            <?php
            foreach ($second_round_tours as $index_outer => $tour) {
                ?>
                <div class="my-table">
                <?php
                echo '<h4>ТУР НОМЕР ' . ($index_outer + 1) . '</h4>';

                echo '<table>';
                ?>
                
                <tr style="background-color:beige;">
                    <th>Хозяева</th>
                    <th>Гости</th>
                </tr>

                <?php
                foreach ($tour->get_matches() as $index_inner => $match) {
                    ?>
                    <?php $host_class_name = str_replace(' ', '', $match->get_host()); ?>
                    <?php $guest_class_name = str_replace(' ', '', $match->get_guest()); ?>

                    <tr class="<?= $host_class_name; ?> <?= $guest_class_name; ?>">
                        <td class="<?= $host_class_name; ?>">
                            <?= $match->get_host(); ?>
                        </td>
                        <td class="<?= $guest_class_name; ?>" >
                            <?= $match->get_guest(); ?>
                        </td>
                    </tr>
                    <?php
                }
                echo '</table>';
                ?>

                </div>
                <?php
            }
            ?>
        </div>
    </div>

    

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
