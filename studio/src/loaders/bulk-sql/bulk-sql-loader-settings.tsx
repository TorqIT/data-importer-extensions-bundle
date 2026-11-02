import React from "react";
import { Input } from "antd";
import { Form, FormKit, Select } from "@pimcore/studio-ui-bundle/components";
import { useConnections } from "./useConnections";

export function BulkSqlLoaderSettings(): React.JSX.Element {
    const { connections, isLoading } = useConnections();

    return (
      <FormKit.Panel>
        <Form.Item
          label="Connection"
          name={ ["loaderConfig", "settings", "connection"] }
          required
          rules={ [{ required: true, message: "Connection is required." }] }
        >
          <Select
            loading={ isLoading }
            options={ connections }
          />
        </Form.Item>
        <Form.Item
          extra="e.g. a, b, c"
          label="SELECT"
          name={ ["loaderConfig", "settings", "select"] }
          required
          rules={ [{ required: true, message: "SELECT is required." }] }
        >
          <Input.TextArea autoSize={ { minRows: 3, maxRows: 10 } } />
        </Form.Item>
        <Form.Item
          extra="e.g. table_name t INNER JOIN other_table o ON t.id = o.t_id"
          label="FROM"
          name={ ["loaderConfig", "settings", "from"] }
          required
          rules={ [{ required: true, message: "FROM is required." }] }
        >
          <Input.TextArea autoSize={ { minRows: 3, maxRows: 10 } } />
        </Form.Item>
        <Form.Item
          extra="e.g. t.status = 'active'"
          label="WHERE"
          name={ ["loaderConfig", "settings", "where"] }
        >
          <Input.TextArea autoSize={ { minRows: 3, maxRows: 10 } } />
        </Form.Item>
        <Form.Item
          extra="e.g. t.id, t.name"
          label="GROUP BY"
          name={ ["loaderConfig", "settings", "groupBy"] }
        >
          <Input.TextArea autoSize={ { minRows: 3, maxRows: 10 } } />
        </Form.Item>
        <Form.Item
          label="LIMIT"
          name={ ["loaderConfig", "settings", "limit"] }
        >
          <Input style={ { width: 120 } } />
        </Form.Item>
      </FormKit.Panel>
    );
}
