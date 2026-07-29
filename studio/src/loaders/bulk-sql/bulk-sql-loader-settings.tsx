import React from "react";
import { Input } from "antd";
import { Form, FormKit, Select } from "@pimcore/studio-ui-bundle/components";
import { useConnections } from "./useConnections";

export function BulkSqlLoaderSettings(): React.JSX.Element {
    const { connections, isLoading } = useConnections();

    return (
        <FormKit.Panel>
            <Form.Item
                name={["loaderConfig", "settings", "connection"]}
                label="Connection"
                required
                rules={[{ required: true, message: "Connection is required." }]}
            >
                <Select loading={isLoading} options={connections} />
            </Form.Item>
            <Form.Item
                name={["loaderConfig", "settings", "select"]}
                label="SELECT"
                extra="e.g. a, b, c"
                required
                rules={[{ required: true, message: "SELECT is required." }]}
            >
                <Input.TextArea autoSize={{ minRows: 3, maxRows: 10 }} />
            </Form.Item>
            <Form.Item
                name={["loaderConfig", "settings", "from"]}
                label="FROM"
                extra="e.g. table_name t INNER JOIN other_table o ON t.id = o.t_id"
                required
                rules={[{ required: true, message: "FROM is required." }]}
            >
                <Input.TextArea autoSize={{ minRows: 3, maxRows: 10 }} />
            </Form.Item>
            <Form.Item name={["loaderConfig", "settings", "where"]} label="WHERE" extra="e.g. t.status = 'active'">
                <Input.TextArea autoSize={{ minRows: 3, maxRows: 10 }} />
            </Form.Item>
            <Form.Item name={["loaderConfig", "settings", "groupBy"]} label="GROUP BY" extra="e.g. t.id, t.name">
                <Input.TextArea autoSize={{ minRows: 3, maxRows: 10 }} />
            </Form.Item>
            <Form.Item name={["loaderConfig", "settings", "limit"]} label="LIMIT">
                <Input style={{ width: 120 }} />
            </Form.Item>
        </FormKit.Panel>
    );
}
