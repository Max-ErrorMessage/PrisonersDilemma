<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Game Admin Panel</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f9fafb;
        }

        .layout {
            display: grid;
            grid-template-columns: 220px 1fr;
            height: 100vh;
        }

        .sidebar {
            background: #111827;
            color: white;
            padding: 20px;
        }

        .sidebar h2 {
            font-size: 18px;
            margin-bottom: 20px;
        }

        .sidebar a {
            display: block;
            color: #9ca3af;
            padding: 10px;
            text-decoration: none;
            border-radius: 6px;
        }

        .sidebar a:hover {
            background: #1f2937;
            color: white;
        }

        .main {
            padding: 24px;
            overflow-y: auto;
        }

        .card {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.05);
            margin-bottom: 20px;
        }

        h1 {
            margin-top: 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 10px;
            border-bottom: 1px solid #e5e7eb;
            text-align: left;
        }

        tr:hover {
            background: #f3f4f6;
        }

        input, select {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            margin-bottom: 15px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
        }

        button {
            padding: 10px 16px;
            border: none;
            border-radius: 6px;
            background: #2563eb;
            color: white;
            cursor: pointer;
        }

        button:hover {
            opacity: 0.9;
        }

        .card ul {
          padding-left: 20px;
          margin: 10px 0 0 0;
        }

        .card li {
          margin-bottom: 6px;
        }
        .cards {
          display: grid;
          grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
          gap: 16px;
        }

        .card {
          background: white;
          padding: 16px;
          border-radius: 12px;
          box-shadow: 0 2px 6px rgba(0,0,0,0.05);
        }
    </style>
</head>
<body>

<div class="layout">

    <div class="sidebar">
        <h2>Admin</h2>
        <a href="#">Dashboard</a>
        <a href="#">Characters</a>
        <a href="#">Cards</a>
        <a href="#">Abilities</a>
    </div>

    <div class="main">

        <h1>Dashboard</h1>

        <!-- DATABASE VIEW -->
        <div class="card">
            <h2>Characters</h2>

            <table>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Attack</th>
                    <th>Income</th>
                    <th>HP</th>
                </tr>

                <?php
                $conn = new mysqli("localhost", "JameGame", "SuPeRsTr0nGpAs5w0rD|", "JameGame");
                $result = $conn->query("SELECT * FROM Characters");

                while($row = $result->fetch_assoc()) {
                    echo "<tr>\n";
                    echo "<td>" . $row['chr_id'] . "</td>\n";
                    echo "<td>" . $row['name'] . "</td>\n";
                    echo "<td>" . $row['attack'] . "</td>\n";
                    echo "<td>" . $row['income'] . "</td>\n";
                    echo "<td>" . $row['hp'] . "</td>\n";
                    echo "</tr>\n";
                }
                ?>

            </table>
        </div>

        <!-- INSERT FORM Char-->
        <div class="card">
            <h2>Add Character</h2>

            <form method="POST">
                <label>Name</label>
                <input type="text" name="char_name" required>
                <label>Attack</label>
                <input type="text" name="char_dmg" required>
                <label>Income</label>
                <input type="text" name="char_inc" required>
                <label>HP</label>
                <input type="text" name="char_hp" required>

                <button type="submit" name="add_char">Add Character</button>
            </form>

            <?php
            if(isset($_POST['add_char'])) {
                $name = $conn->real_escape_string($_POST['char_name']);
                $dmg = $conn->real_escape_string($_POST['char_dmg']);
                $inc = $conn->real_escape_string($_POST['char_inc']);
                $hp = $conn->real_escape_string($_POST['char_hp']);
                $conn->query("INSERT INTO characters (name, attack, income, hp) VALUES ('$name', '$dmg', '$inc', '$hp')");
                echo "<p>Character added!</p>";
            }
            ?>
        </div>

        <!-- DATABASE VIEW -->
        <div class="card">
            <h2>Cards</h2>


            <?php
            $result = $conn->query("
Select
c.c_id, c.name as c_name, c.cost, a1.name as name, ca.value as v, s1.name as status_1, a2.name as name_1, cona.true_value, s2.name as status_2, a3.name as name_2, cona.false_value, s3.name as status_3, con.subject, con.type, con.operator, con.value
from Cards c
inner join CardAbilities ca
on c.c_id = ca.c_id
left join ConditionalAbilities cona
on cona.cab_id = ca.c_ab_id
left join Abilities a1
on a1.ab_id = ca.ab_id
left join Abilities a2
on a2.ab_id = cona.true_ab_id
left join Abilities a3
on a3.ab_id = cona.false_ab_id
left join Status s1
on s1.s_id = ca.status
left join Status s2
on s2.s_id = cona.true_status_id
left join Status s3
on s3.s_id = cona.false_status_id
left join Conditions con
on con.con_id = cona.con_id;
            ");
            $cards = [];

            while($row = $result->fetch_assoc()) {
                $id = $row['c_id'];

                if (!isset($cards[$id])) {
                    $cards[$id] = [
                        "name" => $row['c_name'],
                        "cost" => $row['cost'],
                        "abilities" => []
                    ];
                }

                // Build ability description
                if ($row['name'] == "Conditional") {
                    $ability = "If {$row['subject']} {$row['type']} {$row['operator']} {$row['value']} → "
                                . "{$row['name_1']} {$row['true_value']} else {$row['name_2']} {$row['false_value']}";
                } else {
                    $ability = $row['name'] . ": " . $row['v'];

                    if ($row['status_1']) {
                        $ability .= " (" . $row['status_1'] . ")";
                    }
                }

                $cards[$id]['abilities'][] = $ability;
            }
            echo "<div class='cards'>";
            foreach ($cards as $card) {
                echo "<div class='card'>";
    
                echo "<h2>{$card['name']}</h2>";
                echo "<p><strong>Cost:</strong> {$card['cost']}</p>";

                echo "<ul>";
                foreach ($card['abilities'] as $ability) {
                    echo "<li>$ability</li>";
                }
                echo "</ul>";

                echo "</div>";
            }
            echo "</div>";
            ?>

        </div>

        <?php
        // Fetch lookup lists for the Add Card form
        $abilitiesList = [];
        $res = $conn->query("SELECT ab_id, name FROM Abilities ORDER BY name");
        while ($r = $res->fetch_assoc()) {
            $abilitiesList[] = $r;
        }

        $statusList = [];
        $res = $conn->query("SELECT s_id, name FROM Status ORDER BY name");
        while ($r = $res->fetch_assoc()) {
            $statusList[] = $r;
        }

        // Fetch distinct condition types only (used for the "Type" dropdown in the conditional builder)
        $conditionTypes = [];
        $res = $conn->query("SELECT DISTINCT `type` FROM Conditions ORDER BY `type`");
        while ($r = $res->fetch_assoc()) {
            $conditionTypes[] = $r['type'];
        }

        // Keep the raw conditions list variable (not used for inputs anymore) in case other logic expects it
        $conditionsList = [];
        $res = $conn->query("SELECT con_id, subject, `type`, `operator`, `value` FROM Conditions ORDER BY con_id");
        while ($r = $res->fetch_assoc()) {
            $label = trim($r['subject'] . ' ' . $r['type'] . ' ' . $r['operator'] . ' ' . $r['value']);
            $conditionsList[] = ['con_id' => $r['con_id'], 'label' => $label];
        }
        ?>

        <!-- INSERT FORM Card-->
        <div class="card">
            <h2>Add Card</h2>

            <form method="POST">

              <div class="card">
                <h2>Create Card</h2>

                <label>Name</label>
                <input name="name">

                <label>Cost</label>
                <input name="cost" type="number">
              </div>

              <div id="abilities"></div>

              <button type="button" onclick="addAbility()">+ Add Ability</button>
              <button type="submit">Save Card</button>

            </form>

            <?php
            if(isset($_POST['add_char'])) {
                $name = $_POST['card_name'];
                $conn->query("INSERT INTO Cards (name) VALUES ('$name')");
                echo "<p>Character added!</p>";
            }
            ?>
        </div>

        <!-- CUSTOM QUERY AREA -->
        <div class="card">
            <h2>Custom Query</h2>

            <form method="POST">
                <label>SQL Query</label>
                <input type="text" name="query" placeholder="INSERT / UPDATE / SELECT...">

                <button type="submit" name="run_query">Run</button>
            </form>

            <?php
            if(isset($_POST['run_query'])) {
                $query = $_POST['query'];
                $result = $conn->query($query);

                if($result === TRUE) {
                    echo "<p>Query executed successfully.</p>";
                } else if($result) {
                    echo "<table>";
                    while($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        foreach($row as $val) {
                            echo "<td>$val</td>";
                        }
                        echo "</tr>";
                    }
                    echo "</table>";
                } else {
                    echo "<p>Error: " . $conn->error . "</p>";
                }
            }
            ?>
        </div>

    </div>

</div>

</body>
<script>
/* Data from server-side lookups */
const ABILITIES = <?php echo json_encode($abilitiesList); ?>;
const STATUSES = <?php echo json_encode($statusList); ?>;
const CONDITION_TYPES = <?php echo json_encode($conditionTypes); ?>;
/* legacy/raw conditions list (not used for auto-selecting conditions anymore) */
const CONDITIONS = <?php echo json_encode($conditionsList); ?>;

let abilityCount = 0;

function buildOptions(list, valueKey = Object.keys(list[0] || {})[0], labelKey = Object.keys(list[0] || {})[1]) {
  if (!list || list.length === 0) return '';
  return list.map(item => `<option value="${item[valueKey]}">${item[labelKey]}</option>`).join('');
}

function addAbility() {
  const container = document.getElementById("abilities");

  const id = abilityCount;
  const div = document.createElement("div");
  div.className = "card";

  // Build ability select options (value = ab_id, text = name)
  const abilityOptions = ABILITIES.map(a => `<option value="${a.ab_id}">${a.name}</option>`).join('');
  const statusOptions = STATUSES.map(s => `<option value="${s.s_id}">${s.name}</option>`).join('');
  const conditionTypeOptions = CONDITION_TYPES.map(t => `<option value="${t}">${t}</option>`).join('');

  div.innerHTML = `
    <h3>Ability</h3>

    <label>Type (Ability)</label>
    <select name="abilities[${id}][ab_id]" onchange="onMainAbilityChange(this, ${id})">
      <option value="">-- Select Ability --</option>
      ${abilityOptions}
    </select>

    <div id="value_wrapper_${id}">
      <label>Value</label>
      <input name="abilities[${id}][value]">
    </div>

    <div id="status_wrapper_${id}" style="display:none;">
      <label>Status</label>
      <select name="abilities[${id}][status_id]" id="status_${id}">
        <option value="">-- Select Status --</option>
        ${statusOptions}
      </select>
    </div>

    <div id="conditional_${id}" style="display:none;">
      <h4>Condition</h4>

      <label>Subject</label>
      <select name="abilities[${id}][condition][subject]">
        <option value="self">self</option>
        <option value="opponent">opponent</option>
      </select>

      <label>Type</label>
      <select name="abilities[${id}][condition][type]">
        <option value="">-- Select Type --</option>
        ${conditionTypeOptions}
      </select>

      <label>Operator</label>
      <select name="abilities[${id}][condition][operator]">
        <option value="<">&lt;</option>
        <option value="=">=</option>
        <option value=">">&gt;</option>
      </select>

      <label>Value</label>
      <input name="abilities[${id}][condition][value]" placeholder="Enter comparison value">

      <h4>True</h4>
      <label>Ability</label>
      <select name="abilities[${id}][true][ab_id]" onchange="onConditionalAbilityChange(this, ${id}, 'true')">
        <option value="">-- Select Ability --</option>
        ${abilityOptions}
      </select>
      <label>Value</label>
      <input name="abilities[${id}][true][value]">
      <div id="status_wrapper_${id}_true" style="display:none;">
        <label>Status</label>
        <select name="abilities[${id}][true][status_id]">
          <option value="">-- Select Status --</option>
          ${statusOptions}
        </select>
      </div>

      <h4>False</h4>
      <label>Ability</label>
      <select name="abilities[${id}][false][ab_id]" onchange="onConditionalAbilityChange(this, ${id}, 'false')">
        <option value="">-- Select Ability --</option>
        ${abilityOptions}
      </select>
      <label>Value</label>
      <input name="abilities[${id}][false][value]">
      <div id="status_wrapper_${id}_false" style="display:none;">
        <label>Status</label>
        <select name="abilities[${id}][false][status_id]">
          <option value="">-- Select Status --</option>
          ${statusOptions}
        </select>
      </div>
    </div>
  `;

  container.appendChild(div);
  abilityCount++;
}

function onMainAbilityChange(select, id) {
  const selectedText = select.options[select.selectedIndex]?.text || '';
  // Show conditional area only if ability name is "Conditional"
  const conditionalSection = document.getElementById("conditional_" + id);
  conditionalSection.style.display = (selectedText === "Conditional") ? "block" : "none";

  // Show status selector only if ability name is "Status"
  const statusWrapper = document.getElementById("status_wrapper_" + id);
  statusWrapper.style.display = (selectedText === "Status") ? "block" : "none";

  // Hide top-level value input when conditional is selected; show otherwise
  const valueWrapper = document.getElementById("value_wrapper_" + id);
  valueWrapper.style.display = (selectedText === "Conditional") ? "none" : "block";
}

function onConditionalAbilityChange(select, id, which) {
  const selectedText = select.options[select.selectedIndex]?.text || '';
  const wrapper = document.getElementById(`status_wrapper_${id}_${which}`);
  wrapper.style.display = (selectedText === "Status") ? "block" : "none";
}
</script>
</html>
