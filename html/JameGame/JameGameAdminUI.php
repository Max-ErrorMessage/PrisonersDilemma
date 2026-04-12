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

<?php
// Connection (with error handling)
$conn = new mysqli("localhost", "JameGame", "SuPeRsTr0nGpAs5w0rD|", "JameGame");
if ($conn->connect_error) {
    die("<div class='main'><div class='card'><p>Database connection failed: " . htmlspecialchars($conn->connect_error) . "</p></div></div></body></html>");
}
$conn->set_charset('utf8mb4');
?>

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
                $result = $conn->query("SELECT * FROM Characters");
                if ($result) {
                    while($row = $result->fetch_assoc()) {
                        echo "<tr>\n";
                        echo "<td>" . htmlspecialchars($row['chr_id']) . "</td>\n";
                        echo "<td>" . htmlspecialchars($row['name']) . "</td>\n";
                        echo "<td>" . htmlspecialchars($row['attack']) . "</td>\n";
                        echo "<td>" . htmlspecialchars($row['income']) . "</td>\n";
                        echo "<td>" . htmlspecialchars($row['hp']) . "</td>\n";
                        echo "</tr>\n";
                    }
                    $result->free();
                } else {
                    echo "<tr><td colspan='5'>Error fetching characters: " . htmlspecialchars($conn->error) . "</td></tr>";
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
                <input type="number" name="char_dmg" required>
                <label>Income</label>
                <input type="number" name="char_inc" required>
                <label>HP</label>
                <input type="number" name="char_hp" required>

                <button type="submit" name="add_char">Add Character</button>
            </form>

            <?php
            if(isset($_POST['add_char'])) {
                // sanitize / validate
                $name = trim($_POST['char_name']);
                $attack = intval($_POST['char_dmg']);
                $income = intval($_POST['char_inc']);
                $hp = intval($_POST['char_hp']);

                $stmt = $conn->prepare("INSERT INTO Characters (name, attack, income, hp) VALUES (?, ?, ?, ?)");
                if ($stmt) {
                    $stmt->bind_param("siii", $name, $attack, $income, $hp);
                    if ($stmt->execute()) {
                        echo "<p>Character added!</p>";
                    } else {
                        echo "<p>Error adding character: " . htmlspecialchars($stmt->error) . "</p>";
                    }
                    $stmt->close();
                } else {
                    echo "<p>Prepare failed: " . htmlspecialchars($conn->error) . "</p>";
                }
            } else {
                echo "<p>Fill out the form to add a new character.</p>";
            }
            ?>
        </div>
                                                
        <!-- DATABASE VIEW -->
        <div class="card">
            <h2>Cards</h2>


            <?php
            $cards = [];
            $sql = "
Select
c.c_id, c.name as c_name, c.cost, a1.name as ability_name, ca.value as v, s1.name as status_1, a2.name as name_1, cona.true_value, s2.name as status_2, a3.name as name_2, cona.false_value, s3.name as status_3, con.subject, con.type, con.operator, con.value as con_value
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
            ";
            $result = $conn->query($sql);
            if ($result) {
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
                    if ($row['ability_name'] === "Conditional") {
                        $ability = "If " . htmlspecialchars($row['subject']) . " " . htmlspecialchars($row['type']) . " " . htmlspecialchars($row['operator']) . " " . htmlspecialchars($row['con_value'])
                                    . " → " . htmlspecialchars($row['name_1']) . " " . htmlspecialchars($row['true_value'])
                                    . " else " . htmlspecialchars($row['name_2']) . " " . htmlspecialchars($row['false_value']);
                    } else {
                        $ability = htmlspecialchars($row['ability_name']) . ": " . htmlspecialchars($row['v']);

                        if (!empty($row['status_1'])) {
                            $ability .= " (" . htmlspecialchars($row['status_1']) . ")";
                        }
                    }

                    $cards[$id]['abilities'][] = $ability;
                }
                $result->free();
            } else {
                echo "<p>Error fetching cards: " . htmlspecialchars($conn->error) . "</p>";
            }

            echo "<div class='cards'>";
            foreach ($cards as $card) {
                echo "<div class='card'>";

                echo "<h2>" . htmlspecialchars($card['name']) . "</h2>";
                echo "<p><strong>Cost:</strong> " . htmlspecialchars($card['cost']) . "</p>";

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
        if ($res) {
            while ($r = $res->fetch_assoc()) {
                $abilitiesList[] = $r;
            }
            $res->free();
        }

        $statusList = [];
        $res = $conn->query("SELECT s_id, name FROM Status ORDER BY name");
        if ($res) {
            while ($r = $res->fetch_assoc()) {
                $statusList[] = $r;
            }
            $res->free();
        }

        // Fetch distinct condition types only (used for the "Type" dropdown in the conditional builder)
        $conditionTypes = [];
        $res = $conn->query("SELECT DISTINCT `type` FROM Conditions ORDER BY `type`");
        if ($res) {
            while ($r = $res->fetch_assoc()) {
                $conditionTypes[] = $r['type'];
            }
            $res->free();
        }

        // Keep the raw conditions list variable (not used for inputs anymore) in case other logic expects it
        $conditionsList = [];
        $res = $conn->query("SELECT con_id, subject, `type`, `operator`, `value` FROM Conditions ORDER BY con_id");
        if ($res) {
            while ($r = $res->fetch_assoc()) {
                $label = trim($r['subject'] . ' ' . $r['type'] . ' ' . $r['operator'] . ' ' . $r['value']);
                $conditionsList[] = ['con_id' => $r['con_id'], 'label' => $label];
            }
            $res->free();
        }
        ?>

        <!-- INSERT FORM Card-->
        <div class="card">
            <h2>Add Card</h2>

            <form method="POST">

              <div class="card">
                <h2>Create Card</h2>

                <label>Name</label>
                <input name="card_name" required>

                <label>Cost</label>
                <input name="card_cost" type="number" required>
              </div>

              <div id="abilities"></div>

              <button type="button" onclick="addAbility()">+ Add Ability</button>
              <button type="submit" name="save_card">Save Card</button>

            </form>

            <?php
            if(isset($_POST['save_card'])) {
                // Assumptions / mapping used by this save logic:
                // #-=-=-=-=-=-=-=-=-=-=-=-=-=- [details] -=-=-=-=-=-=-=-
                // CardAbilities table assumed columns: c_ab_id (PK), c_id, ab_id, value, status
                // ConditionalAbilities table assumed columns: cab_id (FK->CardAbilities.c_ab_id), true_ab_id, true_value, true_status_id, false_ab_id, false_value, false_status_id, con_id
                // Conditions table assumed columns: con_id (PK), subject, `type`, `operator`, `value`
                // Ability id for Conditional = 5 (as described by you). If different, adjust the conditional check.
                // If your real column names differ (for example different status field name), update the queries accordingly.
                // #-=-=-=-=-=-=-=-=-=-=-=-=-=- [end details] -=-=-=-=-=-=-=-

                $name = trim($_POST['card_name'] ?? '');
                $cost = intval($_POST['card_cost'] ?? 0);

                if ($name === '') {
                    echo "<p>Please provide a card name.</p>";
                } else {
                    // Start transaction so all card + abilities + conditional pieces are atomic
                    $conn->begin_transaction();
                    $ok = true;

                            $stmt = $conn->prepare("INSERT INTO Cards (name, cost) VALUES (?, ?)");
                    if (!$stmt) {
                        echo "<p>Prepare failed (Cards): " . htmlspecialchars($conn->error) . "</p>";
                        $ok = false;
                    } else {
                        $stmt->bind_param("si", $name, $cost);
                        if (!$stmt->execute()) {
                            echo "<p>Error adding card: " . htmlspecialchars($stmt->error) . "</p>";
                            $ok = false;
                        } else {
                            $card_id = $stmt->insert_id;
                        }
                        $stmt->close();
                    }

                    // Process abilities if card insert succeeded
                    if ($ok) {
                        $abilities = $_POST['abilities'] ?? [];
                        foreach ($abilities as $idx => $ab) {
                            // sanitize common fields
                            $ab_id = intval($ab['ab_id'] ?? 0);
                            if ($ab_id <= 0) continue; // skip empty ability selects

                            // Conditional ability (ab_id === 5 per your description)
                            if ($ab_id === 5) {
                                // Insert placeholder CardAbilities row that references the card
                                $stmtCA = $conn->prepare("INSERT INTO CardAbilities (c_id, ab_id, value, status) VALUES (?, ?, NULL, NULL)");
                                if (!$stmtCA) {
                                    echo "<p>Prepare failed (CardAbilities conditional): " . htmlspecialchars($conn->error) . "</p>";
                                    $ok = false; break;
                                }
                                $stmtCA->bind_param("ii", $card_id, $ab_id);
                                if (!$stmtCA->execute()) {
                                    echo "<p>Error inserting CardAbilities (conditional): " . htmlspecialchars($stmtCA->error) . "</p>";
                                    $stmtCA->close();
                                    $ok = false; break;
                                }
                                $cab_id = $conn->insert_id;
                                $stmtCA->close();

                                // Insert condition row
                                $cond = $ab['condition'] ?? [];
                                $subject = trim($cond['subject'] ?? '');
                                $type = trim($cond['type'] ?? '');
                                $operator = trim($cond['operator'] ?? '');
                                $cond_value = isset($cond['value']) ? trim($cond['value']) : null;

                                $stmtCond = $conn->prepare("INSERT INTO Conditions (subject, `type`, `operator`, `value`) VALUES (?, ?, ?, ?)");
                                if (!$stmtCond) {
                                    echo "<p>Prepare failed (Conditions): " . htmlspecialchars($conn->error) . "</p>";
                                    $ok = false; break;
                                }
                                $stmtCond->bind_param("ssss", $subject, $type, $operator, $cond_value);
                                if (!$stmtCond->execute()) {
                                    echo "<p>Error inserting Condition: " . htmlspecialchars($stmtCond->error) . "</p>";
                                    $stmtCond->close();
                                    $ok = false; break;
                                }
                                $con_id = $conn->insert_id;
                                $stmtCond->close();

                                // True branch
                                $true = $ab['true'] ?? [];
                                $true_ab_id = intval($true['ab_id'] ?? 0) ?: null;
                                $true_value = (isset($true['value']) && $true['value'] !== '') ? $true['value'] : null;
                                $true_status = intval($true['status_id'] ?? 0) ?: null;

                                // False branch
                                $false = $ab['false'] ?? [];
                                $false_ab_id = intval($false['ab_id'] ?? 0) ?: null;
                                $false_value = (isset($false['value']) && $false['value'] !== '') ? $false['value'] : null;
                                $false_status = intval($false['status_id'] ?? 0) ?: null;

                                // Insert into ConditionalAbilities linking to the CardAbilities row
                                $stmtConA = $conn->prepare("INSERT INTO ConditionalAbilities (cab_id, true_ab_id, true_value, true_status_id, false_ab_id, false_value, false_status_id, con_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                                if (!$stmtConA) {
                                    echo "<p>Prepare failed (ConditionalAbilities): " . htmlspecialchars($conn->error) . "</p>";
                                    $ok = false; break;
                                }
                                // types: cab_id (i), true_ab_id (i), true_value (s), true_status_id (i), false_ab_id (i), false_value (s), false_status_id (i), con_id (i)
                                $stmtConA->bind_param(
                                    "iisiisii",
                                    $cab_id,
                                    $true_ab_id,
                                    $true_value,
                                    $true_status,
                                    $false_ab_id,
                                    $false_value,
                                    $false_status,
                                    $con_id
                                );
                                if (!$stmtConA->execute()) {
                                    echo "<p>Error inserting ConditionalAbilities: " . htmlspecialchars($stmtConA->error) . "</p>";
                                    $stmtConA->close();
                                    $ok = false; break;
                                }
                                $stmtConA->close();

                            } else {
                                // Normal ability: has value and optional status
                                $value = (isset($ab['value']) && $ab['value'] !== '') ? $ab['value'] : null;
                                $status = intval($ab['status_id'] ?? 0) ?: null;

                                $stmtCA = $conn->prepare("INSERT INTO CardAbilities (c_id, ab_id, value, status) VALUES (?, ?, ?, ?)");
                                if (!$stmtCA) {
                                    echo "<p>Prepare failed (CardAbilities): " . htmlspecialchars($conn->error) . "</p>";
                                    $ok = false; break;
                                }
                                // types: c_id (i), ab_id (i), value (s), status (i)
                                $stmtCA->bind_param("iisi", $card_id, $ab_id, $value, $status);
                                if (!$stmtCA->execute()) {
                                    echo "<p>Error inserting CardAbilities: " . htmlspecialchars($stmtCA->error) . "</p>";
                                    $stmtCA->close();
                                    $ok = false; break;
                                }
                                $stmtCA->close();
                            }
                        } // end foreach abilities
                    } // end if $ok

                    if ($ok) {
                        $conn->commit();
                        echo "<p>Card and abilities added!</p>";
                    } else {
                        $conn->rollback();
                        echo "<p>Transaction rolled back due to errors. Check messages above.</p>";
                    }
                }
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
                } else if($result instanceof mysqli_result) {
                    echo "<table>";
                    // header row
                    $fields = $result->fetch_fields();
                    echo "<tr>";
                    foreach ($fields as $f) {
                        echo "<th>" . htmlspecialchars($f->name) . "</th>";
                    }
                    echo "</tr>";

                    while($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        foreach($row as $val) {
                            echo "<td>" . htmlspecialchars((string)$val) . "</td>";
                        }
                        echo "</tr>";
                    }
                    echo "</table>";
                    $result->free();
                } else {
                    echo "<p>Error: " . htmlspecialchars($conn->error) . "</p>";
                }
            }
            ?>
        </div>

    </div>

</div>

<?php
$conn->close();
?>

</body>
<script>
/* Data from server-side lookups */
const ABILITIES = <?php echo json_encode($abilitiesList ?? []); ?>;
const STATUSES = <?php echo json_encode($statusList ?? []); ?>;
const CONDITION_TYPES = <?php echo json_encode($conditionTypes ?? []); ?>;
/* legacy/raw conditions list (not used for auto-selecting conditions anymore) */
const CONDITIONS = <?php echo json_encode($conditionsList ?? []); ?>;

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
  if (conditionalSection) conditionalSection.style.display = (selectedText === "Conditional") ? "block" : "none";

  // Show status selector only if ability name is "Status"
  const statusWrapper = document.getElementById("status_wrapper_" + id);
  if (statusWrapper) statusWrapper.style.display = (selectedText === "Status") ? "block" : "none";

  // Hide top-level value input when conditional is selected; show otherwise
  const valueWrapper = document.getElementById("value_wrapper_" + id);
  if (valueWrapper) valueWrapper.style.display = (selectedText === "Conditional") ? "none" : "block";
}

function onConditionalAbilityChange(select, id, which) {
  const selectedText = select.options[select.selectedIndex]?.text || '';
  const wrapper = document.getElementById(`status_wrapper_${id}_${which}`);
  if (wrapper) wrapper.style.display = (selectedText === "Status") ? "block" : "none";
}
</script>
</html>
