import React from "react";
import { Input } from "antd";
import { Form, FormKit, Switch } from "@pimcore/studio-ui-bundle/components";

export function BulkXlsxInterpreterSettings(): React.JSX.Element {
    return (
        <FormKit.Panel>
            <Form.Item name={["interpreterConfig", "settings", "sheetName"]} label="Sheet Name">
                <Input placeholder="Sheet1" />
            </Form.Item>
            <Form.Item name={["interpreterConfig", "settings", "headerRow"]} label="Header Row">
                <Input style={{ width: 120 }} />
            </Form.Item>
            <Form.Item
                name={["interpreterConfig", "settings", "saveHeaderName"]}
                valuePropName="checked"
            >
                <Switch labelRight="Save Header Name" size="small" />
            </Form.Item>
            <Form.Item name={["interpreterConfig", "settings", "uniqueColumns"]} label="Unique Columns" extra="Comma-separated column names">
                <Input />
            </Form.Item>
            <Form.Item name={["interpreterConfig", "settings", "rowFilter"]} label="Row Filter" extra="Expression to filter rows">
                <Input />
            </Form.Item>
        </FormKit.Panel>
    );
}
