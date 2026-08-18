import React from "react";
import { Input } from "antd";
import { Form, FormKit, Switch } from "@pimcore/studio-ui-bundle/components";

export function BulkCsvInterpreterSettings(): React.JSX.Element {
    return (
      <FormKit.Panel>
        <Form.Item
          name={ ["interpreterConfig", "settings", "skipFirstRow"] }
          valuePropName="checked"
        >
          <Switch
            labelRight="Skip First Row"
            size="small"
          />
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
          label="Delimiter"
          name={ ["interpreterConfig", "settings", "delimiter"] }
        >
          <Input
            placeholder=","
            style={ { width: 80 } }
          />
        </Form.Item>
        <Form.Item
          label="Enclosure"
          name={ ["interpreterConfig", "settings", "enclosure"] }
        >
          <Input
            placeholder={ '"' }
            style={ { width: 80 } }
          />
        </Form.Item>
        <Form.Item
          label="Escape"
          name={ ["interpreterConfig", "settings", "escape"] }
        >
          <Input
            placeholder="\\"
            style={ { width: 80 } }
          />
        </Form.Item>
      </FormKit.Panel>
    );
}
