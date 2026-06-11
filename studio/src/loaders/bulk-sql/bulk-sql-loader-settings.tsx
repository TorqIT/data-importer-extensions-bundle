import React, { useCallback, useEffect, useState } from "react";
import axios from "axios";
import { Input } from "antd";
import { Form, FormKit, Select } from "@pimcore/studio-ui-bundle/components";

export function BulkSqlLoaderSettings(): React.JSX.Element {
    const [connectionOptions, setConnectionOptions] = useState<Array<{ label: string; value: string }>>([]);
    const [isLoading, setIsLoading] = useState(false);

    const fetchConnections = useCallback(async (controller?: AbortController) => {
        setIsLoading(true);
        try {
            const res = await axios.get("/pimcore-studio/pimcoredataimporter/get-bulk-connections", {
                signal: controller?.signal,
            });
            setConnectionOptions(
                res.data.map((c: { name: string; value: string }) => ({ label: c.name, value: c.value })),
            );
        } catch (e) {
            if (!axios.isCancel(e)) {
                console.error("Unable to fetch bulk SQL connections.");
            }
        } finally {
            setIsLoading(false);
        }
    }, []);

    useEffect(() => {
        const controller = new AbortController();
        fetchConnections(controller);
        return () => {
            controller.abort();
        };
    }, [fetchConnections]);

    return (
        <FormKit.Panel>
            <Form.Item
                name={["loaderConfig", "settings", "connection"]}
                label="Connection"
                required
                rules={[{ required: true, message: "Connection is required." }]}
            >
                <Select loading={isLoading} options={connectionOptions} />
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
