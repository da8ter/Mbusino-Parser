<?php

class MBusinoParser extends IPSModule
{
    public function Create()
    {
        parent::Create();
        $this->RegisterPropertyInteger("JSONVariableID", 0);
    }

    public function ApplyChanges()
    {
        parent::ApplyChanges();

        $jsonVarID = $this->ReadPropertyInteger("JSONVariableID");
        if ($jsonVarID > 0 && @IPS_VariableExists($jsonVarID)) {
            $this->RegisterMessage($jsonVarID, VM_UPDATE);
        }
    }

    public function MessageSink($TimeStamp, $SenderID, $Message, $Data)
    {
        if ($Message == VM_UPDATE) {
            $this->ParseJSONData(GetValue($SenderID));
        }
    }

    public function ParseNow()
    {
        $varID = $this->ReadPropertyInteger("JSONVariableID");
        if ($varID > 0 && @IPS_VariableExists($varID)) {
            $this->ParseJSONData(GetValue($varID));
        } else {
            IPS_LogMessage("MBusino", "Keine gültige JSON-Variable konfiguriert.");
        }
    }

public function RequestAction($Ident, $Value)
{
    switch ($Ident) {
        case 'ParseNow':
            $this->ParseNow();
            break;
        default:
            throw new Exception("Invalid action");
    }
}

    private function ParseJSONData(string $payload)
    {
        $data = json_decode($payload, true);

        if ($data === null) {
            IPS_LogMessage("MBusino", "Fehler: Ungültiges JSON-Format.");
            return;
        }

        $variableMap = [
            "energy" => ["Energie (Wh)", "~Electricity.Wh"],
            "volume" => ["Volumen (m³)", "~Gas"],
            "flow_temperature" => ["Vorlauftemperatur (°C)", "~Temperature"],
            "return_temperature" => ["Rücklauftemperatur (°C)", "~Temperature"],
            "power" => ["Leistung (W)", "~Watt"],
            "power_max" => ["Max. Leistung (W)", "~Watt"],
            "volume_flow" => ["Volumenstrom (m³/h)", "~Flow"],
            "volume_flow_max" => ["Max. Volumenstrom (m³/h)", "~Flow"],
            "temperature_diff" => ["Temperaturdifferenz (K)", "~Temperature"],
            "time_point" => ["Messzeitpunkt", "~UnixTimestamp"],
            "on_time" => ["Betriebszeit (Tage)", ""],
            "error_flags" => ["Fehlermeldungen", ""],
            "model_version" => ["Modellversion", ""],
            "fab_number" => ["Seriennummer", ""],
        ];

        $nameOccurrences = [];
        foreach ($data as $entry) {
            if (isset($entry["name"])) {
                $nameOccurrences[$entry["name"]] = ($nameOccurrences[$entry["name"]] ?? 0) + 1;
            }
        }

        $entryCount = [];
        foreach ($data as $entry) {
            if (!isset($entry["name"]) || !isset($entry["value_scaled"])) {
                continue;
            }

            $name = $entry["name"];
            $value = $entry["value_scaled"];
            $unit = $entry["units"] ?? "";

            if (isset($entry["value_string"]) && ($unit === "YYYYMMDDhhmm" || $unit === "YYYYMMDD")) {
                $value = strtotime($entry["value_string"]);
            }

            if (!isset($variableMap[$name])) {
                $variableMap[$name] = [$name, ""];
            }

            if ($nameOccurrences[$name] > 1) {
                $entryCount[$name] = ($entryCount[$name] ?? 0) + 1;
                $numberedName = $name . "_" . $entryCount[$name];
                $varName = $variableMap[$name][0] . " " . $entryCount[$name];
            } else {
                $numberedName = $name;
                $varName = $variableMap[$name][0];
            }

            $varProfile = $variableMap[$name][1];
            $varIdent = preg_replace('/[^a-zA-Z0-9]/', '_', strtolower($numberedName));
            $varID = @IPS_GetObjectIDByIdent($varIdent, $this->InstanceID);

            if ($varID === false) {
                switch ($name) {
    case "time_point":
    case "on_time":
    case "model_version":
    case "fab_number":
    case "error_flags":
        $type = 1; // Integer
        break;
    default:
        $type = 2; // Float
        break;
}
                $varID = IPS_CreateVariable($type);
                IPS_SetParent($varID, $this->InstanceID);
                IPS_SetName($varID, $varName);
                IPS_SetIdent($varID, $varIdent);

                if ($varProfile !== "") {
                    IPS_SetVariableCustomProfile($varID, $varProfile);
                }
            }

           // Wert nur setzen, wenn er sich geändert hat
if ((string)GetValue($varID) !== (string)$value) {
    SetValue($varID, $value);
}
        }

        IPS_LogMessage("MBusino", "Werte erfolgreich gespeichert.");
    }
}
