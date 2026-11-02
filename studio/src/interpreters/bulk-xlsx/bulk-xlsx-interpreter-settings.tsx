import React from "react";
import { Input } from "antd";
import { Form, FormKit, Switch } from "@pimcore/studio-ui-bundle/components";

export function BulkXlsxInterpreterSettings(): React.JSX.Element {
    return (
      <FormKit.Panel>
        <Form.Item
          label="Sheet Name"
          name={ ["interpreterConfig", "settings", "sheetName"] }
        >
          <Input placeholder="Sheet1" />
        </Form.Item>
        <Form.Item
          label="Header Row"
          name={ ["interpreterConfig", "settings", "headerRow"] }
        >
          <Input style={ { width: 120 } } />
        </Form.Item>
        <Form.Item
          name={ ["interpreterConfig", "settings", "saveHeaderName"] }
          valuePropName="checked"
        >
          <Switch
            labelRight="Save Header Name"
            size="small"
          />
        </Form.Item>
        <Form.Item
          extra="Comma-separated column names"
          label="Unique Columns"
          name={ ["interpreterConfig", "settings", "uniqueColumns"] }
        >
          <Input />
        </Form.Item>
        <Form.Item
          extra="Expression to filter rows"
          label="Row Filter"
          name={ ["interpreterConfig", "settings", "rowFilter"] }
        >
          <Input />
        </Form.Item>
      </FormKit.Panel>
    );
}
