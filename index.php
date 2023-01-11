<?php

// реализован перый описанный алгоритм -> http://chess.sainfo.ru/tabl/tablei.html

// алгоритм работает только для чётного кол-ва
// команд (для нечётного кол-ва не реализована
// поддержка из-за нехватки времени)

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

$tour_count = count($command_names) - 1;
$tour_match_count = count($command_names) / 2;

class FootballMatch {
    public $host;
    public $guest;

    public function get_host_name($command_names) {
        return $command_names[$this->host - 1];
    }
    public function get_guest_name($command_names) {
        return $command_names[$this->guest - 1];
    }

    public function __toString() {
        $host = is_null($this->host) ? '*' : $this->host;
        $guest = is_null($this->guest) ? '*' : $this->guest;
        $left = ($this->host < 10) ? ' ' : '';
        $right = ($this->guest < 10) ? ' ' : '';
        return '[' . $host . $left . ' - ' . $right . $guest . ']';
    }
}

class Tour {
    private $matches;
    private $max_match_count;
    
    public function __construct($max_match_count, $matches) {
        $this->max_match_count = $max_match_count;
        $this->matches = $matches;
    }

    public function get_matches() {
        return $this->matches;
    }
    public function get_max_match_count() {
        return $this->max_match_count;
    }

    public function __toString() {
        $result = '';
        foreach ($this->matches as $match) {
            $result .= $match . ' ';
        }
        return $result;
    }
}

$tours = [];
for ($i = 0; $i < $tour_count; $i++) {
    $matches = [];
    for ($j = 0; $j < $tour_match_count; $j++) {
        $match = new FootballMatch();
        $matches[] = $match;
    }
    
    $tour = new Tour(max_match_count: $tour_match_count, matches: $matches);
    $tours[] = $tour;
}

// первый шаг
foreach ($tours as $index => $tour) {
    $first_match = $tour->get_matches()[0];
    $last_match_index = count($command_names);
    if ($index % 2 == 0) {
        $first_match->guest = $last_match_index;
    }
    else {
        $first_match->host = $last_match_index;
    }
}

// второй шаг
$numb = 1;
foreach ($tours as $tour_index => $tour) {
    foreach ($tour->get_matches() as $match_index => $match) {
        if (!is_null($match->host)) {
            $match->guest = $numb;
        }
        else {
            $match->host = $numb;
        }

        $numb++;
        if ($numb == count($command_names)) {
            $numb = 1;
        }
    }
}

// третий шаг
$numb = count($command_names) - 1;
foreach ($tours as $tour_index => $tour) {
    foreach ($tour->get_matches() as $match_index => $match) {
        if (!is_null($match->guest)) {
            continue;
        }
        $match->guest = $numb;

        $numb--;
        if ($numb == 0) {
            $numb = count($command_names) - 1;
        }
    }
}

function print_tours($tours) {
    foreach ($tours as $index => $tour) {
        if ($index + 1 < 10) {
            echo ' ';
        }
        echo ($index + 1) . ' -> ' . $tour . PHP_EOL;
    }
}

function get_reverse_tour($tour) {
    $reverse_matches = [];
    foreach ($tour->get_matches() as $match) {
        $reverse_match = new FootballMatch();
        $reverse_match->host = $match->guest;
        $reverse_match->guest = $match->host;

        $reverse_matches[] = $reverse_match;
    }
    $reverse_tour = new Tour(
        max_match_count: $tour->get_max_match_count(),
        matches: $reverse_matches
    );
    return $reverse_tour;
}

$reverse_tours = [];
foreach ($tours as $index => $tour) {
    $reverse_tours[] = get_reverse_tour(tour: $tour);
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
<link type="text/css" rel="stylesheet" href="style.css">
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
<script type="text/javascript" src="jquery.js"></script>
</head>

<body>
    <div class="row">
        <div class="column" style="background-color:#90EE90; margin-left: 400px">
            <h3>ТУРЫ ПЕРВОГО КРУГА</h3>
            <?php
            foreach ($tours as $tour_index => $tour) {
                ?>
                <div class="my-table">
                <?php
                echo '<h4>ТУР НОМЕР ' . ($tour_index + 1) . '</h4>';

                echo '<table>';
                ?>
                
                <tr style="background-color:beige;">
                    <th>Хозяева</th>
                    <th>Гости</th>
                </tr>

                <?php
                foreach ($tour->get_matches() as $match_index => $match) {
                    ?>
                    <?php $host_class_name = str_replace(' ', '', $match->get_host_name($command_names)); ?>
                    <?php $guest_class_name = str_replace(' ', '', $match->get_guest_name($command_names)); ?>

                    <tr class="<?= $host_class_name; ?> <?= $guest_class_name; ?>">
                        <td class="<?= $host_class_name; ?>">
                            <?= $match->get_host_name($command_names); ?>
                        </td>
                        <td class="<?= $guest_class_name; ?>" >
                            <?= $match->get_guest_name($command_names); ?>
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
            foreach ($reverse_tours as $tour_index => $tour) {
                ?>
                <div class="my-table">
                <?php
                echo '<h4>ТУР НОМЕР ' . ($tour_index + $tour_count + 1) . '</h4>';

                echo '<table>';
                ?>
                
                <tr style="background-color:beige;">
                    <th>Хозяева</th>
                    <th>Гости</th>
                </tr>

                <?php
                foreach ($tour->get_matches() as $match_index => $match) {
                    ?>
                    <?php $host_class_name = str_replace(' ', '', $match->get_host_name($command_names)); ?>
                    <?php $guest_class_name = str_replace(' ', '', $match->get_guest_name($command_names)); ?>

                    <tr class="<?= $host_class_name; ?> <?= $guest_class_name; ?>">
                        <td class="<?= $host_class_name; ?>">
                            <?= $match->get_host_name($command_names); ?>
                        </td>
                        <td class="<?= $guest_class_name; ?>" >
                            <?= $match->get_guest_name($command_names); ?>
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
