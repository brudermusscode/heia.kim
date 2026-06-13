<?php

use Heiakim\Time\Time;
use Heiakim\Model\Gamemode;
use Heiakim\Model\User;
use Illuminate\Support\Collection;

/**
 * @var int
 */
$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT) ?? 0;

/**
 * @var int
 */
$gumode = filter_input(INPUT_GET, "gumode", FILTER_VALIDATE_INT) ?? 0;

# Validate gumode int.
if (!in_array($gumode, Gamemode::$modes))
  $gumode = 0;

# User doesn't exist?
if (!($User = User::find($id))) :
  include GET_CONTENT_NOTHING;
else :

  /**
   * @var Collection<StatDevelopment>
   */
  $UserStatDevelopment = $User->stat_development()
    ->where("mode", $gumode)
    ->orderBy("created_at", "ASC")
    ->limit(30)
    ->get();

  # No data?
  if (!$UserStatDevelopment->count()) : ?>

    <box-model user-graph outlined posrel rounded=mid fl alic jucc
      style=height:242px;>
      <div fl alic jucc fldircol gap=smol+ slight>
        <div style="height:3.2em;width:3.2em;" fl alic jucc circled filled>
          <mi mid>show_chart</mi>
        </div>
        <p tac>Nothing to show here</p>
      </div>
    </box-model>

  <?php else :

    $graph_data = [];
    $graph_labels = [];

    foreach ($UserStatDevelopment as $StatDev) {
      $graph_data[] = -$StatDev->rank;
      $graph_labels[] = Time::ago($StatDev->created_at, true);
    }

    # Append current rank.
    $graph_data[] = -$User->get_rankings($gumode)->global;
    $graph_labels[] = "Now";

  ?>

    <box-model user-graph outlined posrel rounded=mid flexone>
      <bm-inr size=std>
        <canvas id="user-ranking-graph" animation=fade-in></canvas>
      </bm-inr>

      <script id="jesusislove">
        new Chart(document.find("#user-ranking-graph"), {
          type: 'line',
          data: {
            labels: <?= json_encode($graph_labels); ?>,
            datasets: [{
              label: 'Rank',
              data: <?= json_encode($graph_data); ?>,
              borderColor: "#FF6384",
              fill: false,
              cubicInterpolationMode: 'monotone',
              tension: 0.4,
            }]
          },
          options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
              title: {
                display: false,
              },
              legend: {
                display: false,
              },
              tooltip: {
                enabled: true,
                padding: {
                  top: 10,
                  left: 12,
                  bottom: 2,
                  right: 12,
                },
                cornerRadius: 12,

                callbacks: {
                  title: function(item) {
                    return `${item[0].dataset.label}: ${item[0].formattedValue.replace("-", "")}`;
                  },
                  afterTitle: function(item) {
                    return item[0].label;
                  },
                  label: function(item) {
                    return null;
                  },
                  labelPointStyle: function(item) {
                    return {
                      pointStyle: "triangle",
                      rotation: 0,
                    }
                  }
                }
              },
            },
            animation: {
              duration: 0,
            },
            interaction: {
              intersect: false,
            },
            scales: {
              x: {
                display: true,
              },
              y: {
                display: false,
                suggestedMin: <?= end($graph_data); ?>,
                suggestedMax: <?= $graph_data[0]; ?>
              }
            }
          },
        });
      </script>
    </box-model>

<?php endif;
endif;
