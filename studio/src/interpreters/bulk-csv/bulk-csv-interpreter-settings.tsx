import React from "react";
import { Input } from "antd";
import { Form, FormKit, Switch } from "@pimcore/studio-ui-bundle/components";

export function BulkCsvInterpreterSettings(): React.JSX.Element {
    return (
        <FormKit.Panel>
            <Form.Item
                name={["interpreterConfig", "settings", "skipFirstRow"]}
                valuePropName="checked"
            >
                <Switch labelRight="Skip First Row" size="small" />
            </Form.Item>
            <Form.Item
                name={["interpreterConfig", "settings", "saveHeaderName"]}
                valuePropName="checked"
            >
                <Switch labelRight="Save Header Name" size="small" />
            </Form.Item>
            <Form.Item name={["interpreterConfig", "settings", "delimiter"]} label="Delimiter">
                <Input style={{ width: 80 }} placeholder="," />
            </Form.Item>
            <Form.Item name={["interpreterConfig", "settings", "enclosure"]} label="Enclosure">
                <Input style={{ width: 80 }} placeholder={'"'} />
            </Form.Item>
            <Form.Item name={["interpreterConfig", "settings", "escape"]} label="Escape">
                <Input style={{ width: 80 }} placeholder="\\" />
            </Form.Item>
        </FormKit.Panel>
    );
}
